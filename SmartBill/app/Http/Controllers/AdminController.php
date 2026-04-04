<?php

namespace App\Http\Controllers;

use App\Exports\SlipWorkbookExport;
use App\Models\Merchant;
use App\Models\Slip;
use App\Models\SlipBatch;
use App\Models\SlipTemplate;
use App\Models\TokenLog;
use App\Models\TokenTopupRequest;
use App\Models\User;
use App\Support\WorkspaceUrl;
use App\Support\OwnerUrl;
use App\Services\GeminiService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function dashboard()
    {
        if (request()->routeIs('tenant.dashboard')) {
            return redirect()->route('admin.slip.index');
        }

        return redirect()->to(WorkspaceUrl::current(request(), 'slips'));
    }

    public function billing()
    {
        $user = auth()->user();
        $tokenLogs = TokenLog::where('user_id', $user->id)->latest()->limit(20)->get();
        $topupRequests = TokenTopupRequest::where('user_id', $user->id)->latest()->limit(10)->get();
        $usageThisMonth = TokenLog::where('user_id', $user->id)
            ->where('type', 'usage')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('delta');

        return view('admin.billing', [
            'tokenLogs' => $tokenLogs,
            'topupRequests' => $topupRequests,
            'usageThisMonth' => abs((int) $usageThisMonth),
            'user' => $user,
        ]);
    }

    public function submitTopupRequest(Request $request)
    {
        $data = $request->validate([
            'requested_tokens' => 'required|integer|min:1|max:100000',
            'amount_paid' => 'nullable|numeric|min:0',
            'payment_slip' => 'required|image|max:5120',
            'note' => 'nullable|string|max:1000',
        ]);

        $path = $request->file('payment_slip')->store('topups', 'public');

        TokenTopupRequest::create([
            'user_id' => auth()->id(),
            'requested_tokens' => $data['requested_tokens'],
            'amount_paid' => $data['amount_paid'] ?? null,
            'payment_slip_path' => $path,
            'note' => $data['note'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('status', 'Top-up request submitted. Please wait for super admin approval.');
    }

    // --- Store Management ---
    public function stores()
    {
        $stores = Merchant::withCount('templates')->where('user_id', auth()->id())->latest()->get();
        return view('admin.stores', compact('stores'));
    }

    public function showStore(Merchant $merchant): JsonResponse
    {
        $this->authorizeMerchant($merchant);

        return response()->json([
            'status' => 'success',
            'store' => $merchant->loadCount(['templates', 'slips']),
        ]);
    }

    public function storeStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
        ]);
        $data['user_id'] = auth()->id();
        $data['subdomain'] = $this->resolveRequestedSubdomain($data['name'], null);
        $store = Merchant::create($data);
        $store->users()->syncWithoutDetaching([
            auth()->id() => ['role' => 'owner'],
        ]);

        return response()->json([
            'status' => 'success',
            'store' => $store,
            'workspace_url' => WorkspaceUrl::workspace($request, $store, 'dashboard'),
        ]);
    }

    public function updateStore(Request $request, Merchant $merchant): JsonResponse
    {
        $this->authorizeMerchant($merchant);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
        ]);
        $data['subdomain'] = $this->resolveRequestedSubdomain(
            $data['name'],
            null,
            $merchant->id
        );
        $merchant->update($data);

        return response()->json(['status' => 'success']);
    }

    public function deleteStore(Request $request, Merchant $merchant): JsonResponse
    {
        $this->authorizeMerchantDeletion($merchant);

        $data = $request->validate([
            'confirmation_name' => 'required|string|max:255',
        ]);

        if (trim($data['confirmation_name']) !== $merchant->name) {
            return response()->json([
                'message' => 'Project name confirmation does not match.',
            ], 422);
        }

        if ((int) $request->session()->get('active_project_id') === (int) $merchant->id) {
            $request->session()->forget('active_project_id');
        }

        $merchant->delete();

        return response()->json(['status' => 'success']);
    }

    // --- Extraction Profiles (Templates) ---
    public function merchants()
    {
        $tenant = app('tenant');
        $templates = SlipTemplate::with('merchant')->where('merchant_id', $tenant->id)->latest()->get();
        $stores = collect([$tenant]);
        return view('admin.templates', compact('templates', 'stores'));
    }

    public function storeMerchant(Request $request): JsonResponse
    {
        $tenant = app('tenant');
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $aiFields = $this->normalizedAiFieldDefinitions(['date' => 'Date', 'total' => 'Total']);

        $template = SlipTemplate::create([
            'user_id' => auth()->id(), // Creator
            'merchant_id' => $tenant->id,
            'name' => $request->name,
            'ai_fields' => $aiFields,
            'main_instruction' => 'Extract data accurately.',
            'export_layout' => $this->defaultExportLayoutForFields($aiFields),
        ]);

        return response()->json(['status' => 'success', 'template' => $template]);
    }

    public function editMerchant(SlipTemplate $merchant)
    {
        $this->authorizeTemplate($merchant);
        $tenant = app('tenant');
        $promptFields = $this->normalizedAiFieldDefinitions($merchant->ai_fields ?? []);
        $exportLayout = $this->resolvedExportLayoutForTemplate($merchant);
        $stores = collect([$tenant]);
        return view('admin.template-edit', compact('merchant', 'promptFields', 'exportLayout', 'stores'));
    }

    public function updateMerchantMapping(Request $request, SlipTemplate $merchant): JsonResponse
    {
        $this->authorizeTemplate($merchant);
        $tenant = app('tenant');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'main_instruction' => 'nullable|string',
        ]);

        $aiFields = is_string($request->ai_fields) ? json_decode($request->ai_fields, true) : $request->ai_fields;
        $normalized = $this->normalizedAiFieldDefinitions($aiFields ?: []);

        $merchant->update([
            'name' => $validated['name'],
            'merchant_id' => $tenant->id,
            'main_instruction' => $validated['main_instruction'] ?? $merchant->main_instruction,
            'ai_fields' => $normalized,
            'export_layout' => $normalized,
        ]);

        return response()->json(['status' => 'success']);
    }

    public function deleteMerchant(SlipTemplate $merchant): JsonResponse
    {
        $this->authorizeTemplate($merchant);
        $merchant->delete();

        return response()->json(['status' => 'success']);
    }

    // --- Slip Registry ---
    public function slipReader(Request $request)
    {
        $tenant = app('tenant');
        $templates = SlipTemplate::with('merchant')
            ->where('merchant_id', $tenant->id)
            ->orderBy('name')
            ->get();
        $batches = SlipBatch::where('merchant_id', $tenant->id)
            ->latest('scanned_at')
            ->latest()
            ->get();

        $slipsQuery = $this->baseSlipQueryForTenant($tenant);
        $this->applySlipFilters($slipsQuery, $request);

        $sort = (string) $request->input('sort', 'latest');
        if ($sort === 'oldest') {
            $slipsQuery->orderBy('processed_at')->orderBy('id');
        } else {
            $slipsQuery->orderByDesc('processed_at')->orderByDesc('id');
        }

        $slips = $slipsQuery->paginate(50)->withQueryString();
        $slips->getCollection()->transform(fn (Slip $slip) => $this->decorateSlipForDisplay($slip));

        $workflowOptions = array_filter(
            Slip::workflowOptions(),
            fn (string $key) => $key !== Slip::WORKFLOW_ARCHIVED,
            ARRAY_FILTER_USE_KEY
        );

        $labelSuggestions = $this->baseSlipQueryForTenant($tenant)
            ->get(['slips.id', 'labels'])
            ->pluck('labels')
            ->filter()
            ->flatMap(fn ($labels) => is_array($labels) ? $labels : [])
            ->map(fn (string $label) => trim($label))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $currentCollection = null;
        $currentCollectionSummary = null;
        $currentCollectionId = (int) $request->input('batch_id', 0);

        if ($currentCollectionId > 0) {
            $currentCollection = $batches->firstWhere('id', $currentCollectionId);

            if ($currentCollection) {
                $currentCollectionSummary = [
                    'total_slips' => $currentCollection->slips()->count(),
                    'active_slips' => $currentCollection->slips()->whereNull('archived_at')->count(),
                    'archived_slips' => $currentCollection->slips()->whereNotNull('archived_at')->count(),
                ];
            }
        }

        $isArchivedView = $request->routeIs('admin.slip.archived') || $request->routeIs('workspace.slip.archived');

        return view('admin.slip', [
            'templates' => $templates,
            'batches' => $batches,
            'slips' => $slips,
            'workflowOptions' => $workflowOptions,
            'labelSuggestions' => $labelSuggestions,
            'currentCollection' => $currentCollection,
            'currentCollectionSummary' => $currentCollectionSummary,
            'isArchivedView' => $isArchivedView,
            'activeFilters' => [
                'q' => (string) $request->input('q', ''),
                'date_from' => (string) $request->input('date_from', ''),
                'date_to' => (string) $request->input('date_to', ''),
                'template_id' => (string) $request->input('template_id', ''),
                'batch_id' => (string) $request->input('batch_id', ''),
                'workflow_status' => (string) $request->input('workflow_status', ''),
                'label' => (string) $request->input('label', ''),
                'sort' => $sort,
            ],
        ]);
    }


    public function storeSlipBatch(Request $request): JsonResponse
    {
        $tenant = app('tenant');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'note' => 'nullable|string|max:1000',
        ]);

        $batch = SlipBatch::firstOrCreate(
            [
                'merchant_id' => $tenant->id,
                'name' => trim($data['name']),
            ],
            [
                'created_by' => auth()->id(),
                'note' => $data['note'] ?? null,
                'scanned_at' => now(),
                'status' => 'open',
            ]
        );

        return response()->json([
            'status' => 'success',
            'batch' => [
                'id' => $batch->id,
                'name' => $batch->name,
            ],
            'collection' => [
                'id' => $batch->id,
                'name' => $batch->name,
                'note' => $batch->note,
            ],
        ]);
    }

    public function updateSlipBatch(Request $request, SlipBatch $batch): JsonResponse
    {
        $this->authorizeSlipBatch($batch);

        $tenant = app('tenant');
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('slip_batches', 'name')
                    ->where(fn ($query) => $query->where('merchant_id', $tenant->id))
                    ->ignore($batch->id),
            ],
            'note' => 'nullable|string|max:1000',
        ]);

        $batch->update([
            'name' => trim($data['name']),
            'note' => $data['note'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'collection' => [
                'id' => $batch->id,
                'name' => $batch->name,
                'note' => $batch->note,
            ],
        ]);
    }

    public function editSlip(Slip $slip)
    {
        $this->authorizeSlip($slip);
        $exportColumns = $slip->template->ai_fields ?? [];
        return view('admin.slip-edit', compact('slip', 'exportColumns'));
    }

    public function processSlip(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image',
            'template_id' => 'required',
            'batch_id' => 'nullable|integer',
            'batch_name' => 'nullable|string|max:255',
            'labels' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $path = $request->file('image')->store('slips', 'public');
        $fullPath = Storage::disk('public')->path($path);

        try {
            $tenant = app('tenant');
            $template = $request->template_id === 'auto'
                ? $this->detectTemplateForUser($tenant->id, $fullPath)
                : SlipTemplate::where('merchant_id', $tenant->id)->findOrFail($request->template_id);
            $batch = $this->resolveSlipBatch($request, $tenant, $user);

            $data = $this->geminiService->extractDataFromImage($fullPath, [
                'ai_fields' => $template->ai_fields,
                'main_instruction' => $template->main_instruction,
            ]);

            $slip = Slip::create([
                'user_id' => $user->id,
                'slip_template_id' => $template->id,
                'slip_batch_id' => $batch->id,
                'image_path' => $path,
                'extracted_data' => $data,
                'workflow_status' => Slip::WORKFLOW_REVIEWED,
                'processed_at' => now(),
                'reviewed_at' => now(),
                'labels' => $this->normalizeSlipLabels($request->input('labels')),
            ]);

            $user->decrement('tokens', 1);
            $user->refresh();
            $this->recordTokenUsage($user, $slip, -1, 'Slip scan completed', [
                'template_id' => $template->id,
                'template_name' => $template->name,
                'batch_id' => $batch->id,
                'batch_name' => $batch->name,
            ]);

            return response()->json([
                'status' => 'success',
                'detected_profile' => $request->template_id === 'auto' ? $template->name : null,
                'tokens_remaining' => (int) $user->tokens,
                'batch_name' => $batch->name,
                'collection_name' => $batch->name,
            ]);
        } catch (\Exception $e) {
            Storage::disk('public')->delete($path);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function updateSlip(Request $request, Slip $slip): JsonResponse
    {
        $this->authorizeSlip($slip);
        $data = is_string($request->data) ? json_decode($request->data, true) : $request->data;
        $slip->update([
            'extracted_data' => $data,
            'workflow_status' => Slip::WORKFLOW_REVIEWED,
            'reviewed_at' => now(),
            'labels' => $request->has('labels') ? $this->normalizeSlipLabels($request->input('labels')) : ($slip->labels ?? []),
        ]);
        return response()->json(['status' => 'success']);
    }

    public function bulkUpdateSlips(Request $request)
    {
        $tenant = app('tenant');
        $data = $request->validate([
            'slip_ids' => 'required|array|min:1',
            'slip_ids.*' => 'integer',
            'bulk_action' => 'required|string|in:mark_reviewed,mark_approved,mark_exported,archive,restore,add_label,remove_label',
            'bulk_label' => 'nullable|string|max:255',
        ]);

        $slipIds = collect($data['slip_ids'])->map(fn ($id) => (int) $id)->unique()->values()->all();
        $slips = $this->baseSlipQueryForTenant($tenant)
            ->whereIn('slips.id', $slipIds)
            ->get();

        if ($slips->isEmpty()) {
            return back()->withErrors(['bulk' => 'Select at least one slip before applying a bulk action.']);
        }

        $labels = $this->normalizeSlipLabels($data['bulk_label'] ?? null);
        if (in_array($data['bulk_action'], ['add_label', 'remove_label'], true) && empty($labels)) {
            return back()->withErrors(['bulk_label' => 'Enter at least one label for this bulk action.']);
        }

        foreach ($slips as $slip) {
            switch ($data['bulk_action']) {
                case 'mark_reviewed':
                    $slip->update($this->workflowStatusPayload(Slip::WORKFLOW_REVIEWED));
                    break;
                case 'mark_approved':
                    $slip->update($this->workflowStatusPayload(Slip::WORKFLOW_APPROVED));
                    break;
                case 'mark_exported':
                    $slip->update($this->workflowStatusPayload(Slip::WORKFLOW_EXPORTED));
                    break;
                case 'archive':
                    $slip->update([
                        'workflow_status' => Slip::WORKFLOW_ARCHIVED,
                        'archived_at' => now(),
                    ]);
                    break;
                case 'restore':
                    $slip->update([
                        'workflow_status' => Slip::WORKFLOW_REVIEWED,
                        'archived_at' => null,
                        'reviewed_at' => now(),
                    ]);
                    break;
                case 'add_label':
                    $existingLabels = $this->normalizeSlipLabels($slip->labels ?? []);
                    $slip->update([
                        'labels' => $this->normalizeSlipLabels(array_merge($existingLabels, $labels)),
                    ]);
                    break;
                case 'remove_label':
                    $existingLabels = $this->normalizeSlipLabels($slip->labels ?? []);
                    $slip->update([
                        'labels' => array_values(array_filter($existingLabels, fn ($label) => !in_array($label, $labels, true))),
                    ]);
                    break;
            }
        }

        return back()->with('status', count($slipIds) . ' slip(s) updated successfully.');
    }
    public function updateSlipWorkflow(Request $request, Slip $slip)
    {
        $this->authorizeSlip($slip);
        $allowedStatuses = implode(',', array_diff(array_keys(Slip::workflowOptions()), [Slip::WORKFLOW_ARCHIVED]));
        $data = $request->validate([
            'workflow_status' => 'required|string|in:' . $allowedStatuses,
        ]);

        $slip->update($this->workflowStatusPayload($data['workflow_status']));

        return back()->with('status', 'Slip workflow updated.');
    }
    public function toggleSlipArchive(Request $request, Slip $slip)
    {
        $this->authorizeSlip($slip);
        $data = $request->validate([
            'archive' => 'required|boolean',
        ]);

        if ((bool) $data['archive']) {
            $slip->update([
                'workflow_status' => Slip::WORKFLOW_ARCHIVED,
                'archived_at' => now(),
            ]);

            return back()->with('status', 'Slip archived.');
        }

        $slip->update([
            'workflow_status' => Slip::WORKFLOW_REVIEWED,
            'archived_at' => null,
        ]);

        return back()->with('status', 'Slip restored.');
    }

    public function deleteSlip(Slip $slip): JsonResponse
    {
        $this->authorizeSlip($slip);
        Storage::disk('public')->delete($slip->image_path);
        $slip->delete();
        return response()->json(['status' => 'success']);
    }

    public function exportCenter(Request $request)
    {
        $tenant = app('tenant');
        $templates = SlipTemplate::with('merchant')
            ->where('merchant_id', $tenant->id)
            ->orderBy('name')
            ->get();
        $collections = SlipBatch::where('merchant_id', $tenant->id)
            ->latest('scanned_at')
            ->latest()
            ->get();

        $filteredQuery = $this->baseSlipQueryForTenant($tenant);
        $this->applySlipFilters($filteredQuery, $request);

        $previewSlips = (clone $filteredQuery)
            ->orderByDesc('processed_at')
            ->orderByDesc('id')
            ->limit(12)
            ->get()
            ->map(fn (Slip $slip) => $this->decorateSlipForDisplay($slip));

        $recentExports = $this->baseSlipQueryForTenant($tenant)
            ->whereNotNull('exported_at')
            ->orderByDesc('exported_at')
            ->orderByDesc('id')
            ->limit(12)
            ->get()
            ->map(fn (Slip $slip) => $this->decorateSlipForDisplay($slip));

        $collectionSummary = SlipBatch::query()
            ->where('merchant_id', $tenant->id)
            ->withCount([
                'slips',
                'slips as active_slips_count' => fn ($query) => $query->whereNull('archived_at'),
                'slips as exported_slips_count' => fn ($query) => $query->whereNotNull('exported_at'),
            ])
            ->latest('scanned_at')
            ->latest()
            ->limit(8)
            ->get();

        $workflowOptions = array_filter(
            Slip::workflowOptions(),
            fn (string $key) => $key !== Slip::WORKFLOW_ARCHIVED,
            ARRAY_FILTER_USE_KEY
        );

        return view('admin.exports', [
            'templates' => $templates,
            'collections' => $collections,
            'workflowOptions' => $workflowOptions,
            'previewSlips' => $previewSlips,
            'recentExports' => $recentExports,
            'collectionSummary' => $collectionSummary,
            'stats' => [
                'matching_slips' => (clone $filteredQuery)->count(),
                'archived_matches' => (clone $filteredQuery)->whereNotNull('archived_at')->count(),
                'exported_matches' => (clone $filteredQuery)->whereNotNull('exported_at')->count(),
                'collections' => $collections->count(),
            ],
            'activeFilters' => [
                'q' => (string) $request->input('q', ''),
                'date_from' => (string) $request->input('date_from', ''),
                'date_to' => (string) $request->input('date_to', ''),
                'template_id' => (string) $request->input('template_id', ''),
                'batch_id' => (string) $request->input('batch_id', ''),
                'workflow_status' => (string) $request->input('workflow_status', ''),
                'label' => (string) $request->input('label', ''),
                'archive_scope' => (string) $request->input('archive_scope', 'active'),
            ],
        ]);
    }

    public function exportExcel(Request $request)
    {
        $tenant = app('tenant');
        $filteredQuery = $this->baseSlipQueryForTenant($tenant);
        $this->applySlipFilters($filteredQuery, $request);
        $filteredSlips = $filteredQuery->orderByDesc('processed_at')->get();

        if ($filteredSlips->isEmpty()) {
            return redirect()->to(WorkspaceUrl::current(request(), 'slips'))->with('error', 'No slips matched the current filters for export.');
        }

        $templates = SlipTemplate::where('merchant_id', $tenant->id)
            ->whereIn('id', $filteredSlips->pluck('slip_template_id')->unique())
            ->get();

        if ($templates->isEmpty()) {
            return redirect()->to(WorkspaceUrl::current(request(), 'slips'))->with('error', 'No extraction profiles found to build export schemas.');
        }

        $sheets = [];
        foreach ($templates as $template) {
            $slips = $filteredSlips
                ->where('slip_template_id', $template->id)
                ->sortByDesc('processed_at')
                ->values();

            if ($slips->isEmpty()) {
                continue;
            }

            $exportFields = $template->export_layout ?: $template->ai_fields ?: [];
            if (empty($exportFields)) {
                continue;
            }

            $baseHeadings = ['Processed Date'];
            $baseKeys = [];
            $arrayKeys = [];

            foreach ($exportFields as $field) {
                if (!is_array($field) || !isset($field['key'])) {
                    continue;
                }
                $isArray = false;
                $subKeys = [];
                foreach ($slips as $slip) {
                    $val = $slip->extracted_data[$field['key']] ?? null;
                    if (is_array($val) && count($val) > 0 && is_array(reset($val))) {
                        $isArray = true;
                        foreach ($val as $item) {
                            if (is_array($item)) {
                                foreach (array_keys($item) as $sk) {
                                    $subKeys[$sk] = true;
                                }
                            }
                        }
                    }
                }

                if ($isArray) {
                    $arrayKeys[$field['key']] = array_keys($subKeys);
                } else {
                    $baseHeadings[] = $field['label'];
                    $baseKeys[] = $field['key'];
                }
            }

            $finalHeadings = $baseHeadings;
            foreach ($arrayKeys as $parentKey => $sKeys) {
                $parentLabel = collect($exportFields)->firstWhere('key', $parentKey)['label'] ?? ucfirst($parentKey);
                foreach ($sKeys as $sk) {
                    $finalHeadings[] = $parentLabel . ' - ' . ucfirst($sk);
                }
            }

            $rows = [];
            foreach ($slips as $slip) {
                $data = $slip->extracted_data ?? [];

                $baseRow = [optional($slip->processed_at)->format('Y-m-d H:i:s')];
                foreach ($baseKeys as $key) {
                    $val = $data[$key] ?? '';
                    $baseRow[] = is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : $val;
                }

                if (empty($arrayKeys)) {
                    $rows[] = $baseRow;
                    continue;
                }

                $maxRows = 1;
                $arrayDataCols = [];
                foreach ($arrayKeys as $parentKey => $sKeys) {
                    $list = (isset($data[$parentKey]) && is_array($data[$parentKey])) ? $data[$parentKey] : [];
                    $maxRows = max($maxRows, count($list));
                    $arrayDataCols[$parentKey] = $list;
                }

                for ($i = 0; $i < max(1, $maxRows); $i++) {
                    $row = $baseRow;
                    foreach ($arrayKeys as $parentKey => $sKeys) {
                        $item = $arrayDataCols[$parentKey][$i] ?? [];
                        foreach ($sKeys as $sk) {
                            $row[] = $item[$sk] ?? '';
                        }
                    }
                    $rows[] = $row;
                }
            }

            $sheets[] = [
                'title' => Str::limit($template->name, 31, ''),
                'headings' => $finalHeadings,
                'rows' => $rows,
            ];
        }

        $filteredSlips->each(function (Slip $slip) {
            $slip->update($this->workflowStatusPayload(Slip::WORKFLOW_EXPORTED));
        });

        return Excel::download(
            new SlipWorkbookExport($sheets),
            'smartbill-' . Str::slug($tenant->name ?: 'workspace') . '-' . now()->format('Ymd_His') . '.xlsx'
        );
    }
    private function baseSlipQueryForTenant(Merchant $tenant): Builder
    {
        return Slip::query()
            ->with(['template.merchant', 'batch'])
            ->whereHas('template', function (Builder $query) use ($tenant) {
                $query->where('merchant_id', $tenant->id);
            });
    }

    private function applySlipFilters(Builder $query, Request $request): void
    {
        if ($request->filled('ids')) {
            $ids = array_filter(explode(',', (string) $request->input('ids')), 'is_numeric');
            if (!empty($ids)) {
                $query->whereIn('slips.id', $ids);
            }
        }

        $term = trim((string) $request->input('q', ''));
        if ($term !== '') {
            $query->where(function (Builder $searchQuery) use ($term) {
                if (is_numeric($term)) {
                    $searchQuery->orWhereKey((int) $term);
                }

                $searchQuery
                    ->orWhere('image_path', 'like', '%' . $term . '%')
                    ->orWhereHas('template', function (Builder $templateQuery) use ($term) {
                        $templateQuery->where('name', 'like', '%' . $term . '%');
                    })
                    ->orWhereHas('batch', function (Builder $batchQuery) use ($term) {
                        $batchQuery->where('name', 'like', '%' . $term . '%');
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('processed_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('processed_at', '<=', $request->input('date_to'));
        }

        if ($request->filled('template_id')) {
            $query->where('slip_template_id', (int) $request->input('template_id'));
        }

        if ($request->filled('batch_id')) {
            $query->where('slip_batch_id', (int) $request->input('batch_id'));
        }

        if ($request->filled('workflow_status')) {
            $query->where('workflow_status', $request->input('workflow_status'));
        }

        $label = $this->normalizeSlipLabels($request->input('label'))[0] ?? null;
        if ($label) {
            $query->whereJsonContains('labels', $label);
        }

        $archiveScope = (string) $request->input('archive_scope', 'active');
        if ($archiveScope === 'archived') {
            $query->whereNotNull('archived_at');
        } elseif ($archiveScope !== 'all') {
            $query->whereNull('archived_at');
        }
    }
    private function decorateSlipForDisplay(Slip $slip): Slip
    {
        $data = $slip->extracted_data ?: [];
        $slip->display_shop = $data['shop_name'] ?? $data['store_name'] ?? $slip->template?->merchant?->name ?? 'Unknown';
        $slip->display_date = $data['date'] ?? $data['transaction_date'] ?? optional($slip->processed_at)->format('d/m/Y');
        $slip->display_amount = $data['total_amount'] ?? $data['total'] ?? $data['final_total'] ?? 0;
        $slip->batch_name = $slip->batch?->name ?? 'Inbox';
        $slip->display_labels = $this->normalizeSlipLabels($slip->labels ?? []);

        return $slip;
    }

    private function workflowStatusPayload(string $workflowStatus): array
    {
        $updates = [
            'workflow_status' => $workflowStatus,
        ];

        if ($workflowStatus !== Slip::WORKFLOW_ARCHIVED) {
            $updates['archived_at'] = null;
        }

        if ($workflowStatus === Slip::WORKFLOW_REVIEWED) {
            $updates['reviewed_at'] = now();
        }

        if ($workflowStatus === Slip::WORKFLOW_APPROVED) {
            $updates['approved_at'] = now();
        }

        if ($workflowStatus === Slip::WORKFLOW_EXPORTED) {
            $updates['exported_at'] = now();
        }

        if ($workflowStatus === Slip::WORKFLOW_ARCHIVED) {
            $updates['archived_at'] = now();
        }

        return $updates;
    }

    private function normalizeSlipLabels(array|string|null $labels): array
    {
        if (is_array($labels)) {
            $items = $labels;
        } else {
            $items = preg_split('/[,\r\n]+/', (string) $labels) ?: [];
        }

        return collect($items)
            ->map(fn ($label) => trim((string) $label))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
    private function resolveSlipBatch(Request $request, Merchant $tenant, User $user): SlipBatch
    {
        $batchId = (int) $request->input('batch_id');
        if ($batchId > 0) {
            return SlipBatch::where('merchant_id', $tenant->id)->findOrFail($batchId);
        }

        $batchName = trim((string) $request->input('batch_name', ''));
        if ($batchName !== '') {
            return SlipBatch::firstOrCreate(
                [
                    'merchant_id' => $tenant->id,
                    'name' => $batchName,
                ],
                [
                    'created_by' => $user->id,
                    'status' => 'open',
                    'scanned_at' => now(),
                ]
            );
        }

        return SlipBatch::firstOrCreate(
            [
                'merchant_id' => $tenant->id,
                'name' => 'Inbox ' . now()->format('Y-m-d'),
            ],
            [
                'created_by' => $user->id,
                'status' => 'open',
                'scanned_at' => now(),
            ]
        );
    }
    public function suggestPrompt(Request $request): JsonResponse
    {
        $file = $request->file('image');
        $path = $file->store('temp', 'public');
        $fullPath = Storage::disk('public')->path($path);

        try {
            $raw = $this->geminiService->suggestSchemaFromImage($fullPath);
            $fields = $this->normalizedAiFieldDefinitions($raw);
            return response()->json(['status' => 'success', 'ai_fields' => $fields]);
        } finally {
            Storage::disk('public')->delete($path);
        }
    }

    public function users()
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $users = User::with('merchants')->withCount(['slips', 'tokenTopupRequests', 'tokenLogs'])->latest()->get();
        $merchants = Merchant::query()->orderBy('name')->get();
        $stats = [
            'users' => $users->count(),
            'admins' => $users->filter(fn (User $user) => $user->isAdmin())->count(),
            'activeUsers' => $users->where('status', 'active')->count(),
            'suspended' => $users->where('status', 'suspended')->count(),
            'tokens' => (int) $users->sum('tokens'),
            'slips' => Slip::count(),
            'pendingTopups' => TokenTopupRequest::where('status', 'pending')->count(),
        ];

        return view('admin.users', compact('users', 'stats', 'merchants'));
    }

    public function superAdminDashboard()
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $users = User::query()->latest()->get();
        $projects = Merchant::query()->with(['owner', 'users'])->withCount(['templates', 'slips'])->latest()->get();
        $topupRequests = TokenTopupRequest::query()->with(['user', 'reviewer'])->latest()->limit(8)->get();
        $tokenLogs = TokenLog::query()->latest()->limit(12)->get();

        $stats = [
            'users' => $users->count(),
            'activeUsers' => $users->where('status', 'active')->count(),
            'projects' => $projects->count(),
            'tokens' => (int) $users->sum('tokens'),
            'slips' => Slip::count(),
            'pendingTopups' => TokenTopupRequest::where('status', 'pending')->count(),
            'monthlyUsage' => abs((int) TokenLog::where('type', 'usage')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('delta')),
            'monthlyCredits' => (int) TokenLog::whereIn('type', ['manual_credit', 'manual_topup_approved', 'manual_settlement'])
                ->where('delta', '>', 0)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('delta'),
        ];

        $highBalanceUsers = $users->sortByDesc('tokens')->take(5)->values();
        $activeProjects = $projects->sortByDesc('slips_count')->take(5)->values();
        $recentUsers = $users->take(6);

        return view('admin.super-dashboard', compact(
            'stats',
            'topupRequests',
            'tokenLogs',
            'highBalanceUsers',
            'activeProjects',
            'recentUsers',
        ));
    }

    public function projects()
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $projects = Merchant::with(['owner', 'users'])
            ->withCount(['templates', 'slips'])
            ->latest()
            ->get();

        $owners = User::query()->orderBy('name')->get();

        $stats = [
            'projects' => $projects->count(),
            'archived' => $projects->where('status', 'archived')->count(),
            'memberships' => (int) $projects->sum(fn (Merchant $merchant) => $merchant->users->count()),
            'templates' => (int) $projects->sum('templates_count'),
            'slips' => (int) $projects->sum('slips_count'),
        ];

        return view('admin.projects', compact('projects', 'owners', 'stats'));
    }

    public function storeProjectForAdmin(Request $request)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
        ]);

        $merchant = Merchant::create([
            'name' => $data['name'],
            'subdomain' => $this->resolveRequestedSubdomain($data['name'], null),
            'status' => 'active',
            'user_id' => $data['user_id'] ?? null,
            'address' => $data['address'] ?? null,
            'tax_id' => $data['tax_id'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);

        if (!empty($data['user_id'])) {
            $merchant->users()->syncWithoutDetaching([
                (int) $data['user_id'] => ['role' => 'owner'],
            ]);
        }

        return back()->with('status', 'Project created successfully.');
    }

    public function showProject(Merchant $merchant)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $merchant->load([
            'owner',
            'users' => fn ($query) => $query->orderBy('name'),
            'templates',
        ])->loadCount(['templates', 'slips']);

        $candidateUsers = User::query()->orderBy('name')->get();
        $recentSlips = Slip::with(['user', 'template'])
            ->whereHas('template', function ($query) use ($merchant) {
                $query->where('merchant_id', $merchant->id);
            })
            ->latest('processed_at')
            ->limit(12)
            ->get();

        return view('admin.project-detail', compact('merchant', 'candidateUsers', 'recentSlips'));
    }

    public function updateProjectForAdmin(Request $request, Merchant $merchant)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
        ]);

        $previousOwnerId = $merchant->user_id;

        $merchant->update([
            'name' => $data['name'],
            'subdomain' => $this->resolveRequestedSubdomain($data['name'], null, $merchant->id),
            'user_id' => $data['user_id'] ?? null,
            'address' => $data['address'] ?? null,
            'tax_id' => $data['tax_id'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);

        if (!empty($data['user_id'])) {
            $merchant->users()->syncWithoutDetaching([
                (int) $data['user_id'] => ['role' => 'owner'],
            ]);

            if ($previousOwnerId && (int) $previousOwnerId !== (int) $data['user_id']) {
                $merchant->users()->updateExistingPivot($previousOwnerId, ['role' => 'admin']);
            }
        }

        return back()->with('status', 'Project updated successfully.');
    }

    public function updateProjectStatus(Request $request, Merchant $merchant)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $data = $request->validate([
            'status' => 'required|string|in:active,archived',
        ]);

        $merchant->update([
            'status' => $data['status'],
        ]);

        return back()->with('status', 'Project status updated successfully.');
    }

    public function attachProjectMember(Request $request, Merchant $merchant)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'workspace_role' => 'required|string|in:owner,admin,employee',
        ]);

        $merchant->users()->syncWithoutDetaching([
            (int) $data['user_id'] => ['role' => $data['workspace_role']],
        ]);

        if ($data['workspace_role'] === 'owner') {
            $previousOwnerId = $merchant->user_id;
            $merchant->update(['user_id' => (int) $data['user_id']]);

            if ($previousOwnerId && $previousOwnerId !== (int) $data['user_id']) {
                $merchant->users()->updateExistingPivot($previousOwnerId, ['role' => 'admin']);
            }
        }

        return back()->with('status', 'Project member added successfully.');
    }

    public function updateProjectMemberRole(Request $request, Merchant $merchant, User $user)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $data = $request->validate([
            'workspace_role' => 'required|string|in:owner,admin,employee',
        ]);

        $merchant->users()->syncWithoutDetaching([
            $user->id => ['role' => $data['workspace_role']],
        ]);

        if ($data['workspace_role'] === 'owner') {
            $previousOwnerId = $merchant->user_id;
            $merchant->update(['user_id' => $user->id]);

            if ($previousOwnerId && $previousOwnerId !== $user->id) {
                $merchant->users()->updateExistingPivot($previousOwnerId, ['role' => 'admin']);
            }
        }

        return back()->with('status', 'Project member role updated successfully.');
    }

    public function detachProjectMember(Merchant $merchant, User $user)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        if ((int) $merchant->user_id === (int) $user->id) {
            return back()->withErrors(['project' => 'Primary owner cannot be removed from the project.']);
        }

        $merchant->users()->detach($user->id);

        return back()->with('status', 'Project member removed successfully.');
    }

    public function storeUser(Request $request)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9._-]+$/|unique:users,username',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|integer|in:' . implode(',', [
                User::ROLE_USER,
                User::ROLE_TENANT_ADMIN,
                User::ROLE_SUPER_ADMIN,
            ]),
            'tokens' => 'nullable|integer|min:0|max:100000',
            'merchant_id' => 'nullable|exists:merchants,id',
            'workspace_role' => 'nullable|string|in:owner,admin,employee',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'username' => Str::lower($data['username']),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => (int) $data['role'],
            'status' => 'active',
            'tokens' => (int) ($data['tokens'] ?? 0),
        ]);

        if (!empty($data['merchant_id'])) {
            $merchant = Merchant::findOrFail($data['merchant_id']);
            $merchant->users()->syncWithoutDetaching([
                $user->id => ['role' => $data['workspace_role'] ?? 'employee'],
            ]);
        }

        if (!empty($data['tokens'])) {
            $user->refresh();
            $this->recordTokenEvent(
                $user,
                (int) $data['tokens'],
                'manual_credit',
                'Initial token balance provisioned by super admin',
                ['created_by' => auth()->id(), 'operation' => 'add']
            );
        }

        return back()->with('status', 'User created successfully.');
    }

    public function showUser(User $user)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $user->load([
            'merchants',
            'tokenTopupRequests.reviewer',
        ])->loadCount(['slips', 'tokenLogs']);

        $tokenLogs = $user->tokenLogs()->latest()->limit(25)->get();
        $topupRequests = $user->tokenTopupRequests()->with('reviewer')->latest()->limit(15)->get();
        $availableMerchants = Merchant::query()->orderBy('name')->get();
        $workspaceRoles = $user->merchants()->get()->mapWithKeys(function (Merchant $merchant) {
            return [$merchant->id => $merchant->pivot?->role];
        });

        $workspaceSnapshots = $user->accessibleMerchants()
            ->withCount(['templates', 'slips'])
            ->get()
            ->each(function (Merchant $merchant) use ($workspaceRoles, $user) {
                $merchant->access_role = $workspaceRoles[$merchant->id] ?? (((int) $merchant->user_id === (int) $user->id) ? 'owner' : null);
            });

        $usageSummary = [
            'totalTokenUsage' => abs((int) $user->tokenLogs()->where('type', 'usage')->sum('delta')),
            'manualCredits' => (int) $user->tokenLogs()
                ->whereIn('type', ['manual_credit', 'manual_topup_approved', 'manual_settlement'])
                ->where('delta', '>', 0)
                ->sum('delta'),
            'manualDebits' => abs((int) $user->tokenLogs()
                ->whereIn('type', ['manual_debit', 'manual_settlement'])
                ->where('delta', '<', 0)
                ->sum('delta')),
            'approvedTopups' => (int) $user->tokenLogs()->where('type', 'manual_topup_approved')->sum('delta'),
        ];

        return view('admin.user-detail', compact(
            'user',
            'tokenLogs',
            'topupRequests',
            'availableMerchants',
            'workspaceSnapshots',
            'usageSummary',
        ));
    }

    public function updateUserStatus(Request $request, User $user)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $data = $request->validate([
            'status' => 'required|string|in:active,suspended',
        ]);

        if ((int) $user->id === (int) auth()->id() && $data['status'] !== 'active') {
            return back()->withErrors(['status' => 'You cannot suspend your own super admin account.']);
        }

        $user->update([
            'status' => $data['status'],
        ]);

        return back()->with('status', 'User status updated successfully.');
    }

    public function destroyUser(User $user)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        if ((int) $user->id === (int) auth()->id()) {
            return back()->withErrors(['user' => 'You cannot delete your own super admin account.']);
        }

        if (Merchant::query()->where('user_id', $user->id)->exists()) {
            return back()->withErrors(['user' => 'This user owns one or more projects. Reassign those projects before deleting the account.']);
        }

        $user->delete();

        return redirect()->to(OwnerUrl::path(request(), 'users'))->with('status', 'User deleted successfully.');
    }

    public function updateUserRole(Request $request, User $user)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $data = $request->validate([
            'role' => 'required|integer|in:' . implode(',', [
                User::ROLE_USER,
                User::ROLE_TENANT_ADMIN,
                User::ROLE_SUPER_ADMIN,
            ]),
        ]);

        $user->update([
            'role' => (int) $data['role'],
        ]);

        return back()->with('status', 'User role updated successfully.');
    }

    public function attachUserWorkspace(Request $request, User $user)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $data = $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'workspace_role' => 'required|string|in:owner,admin,employee',
        ]);

        $merchant = Merchant::findOrFail($data['merchant_id']);

        $merchant->users()->syncWithoutDetaching([
            $user->id => ['role' => $data['workspace_role']],
        ]);

        if ($data['workspace_role'] === 'owner') {
            $previousOwnerId = $merchant->user_id;
            $merchant->update(['user_id' => $user->id]);

            if ($previousOwnerId && $previousOwnerId !== $user->id) {
                $merchant->users()->updateExistingPivot($previousOwnerId, ['role' => 'admin']);
            }
        }

        return back()->with('status', 'Workspace access updated successfully.');
    }

    public function detachUserWorkspace(User $user, Merchant $merchant)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        if ((int) $merchant->user_id === (int) $user->id) {
            return back()->withErrors(['workspace' => 'This user is the primary owner of the workspace and cannot be removed here.']);
        }

        $merchant->users()->detach($user->id);

        return back()->with('status', 'Workspace access removed successfully.');
    }

    public function adjustTokens(Request $request, User $user)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $data = $request->validate([
            'operation' => 'required|string|in:add,deduct,set',
            'tokens' => 'required|integer|min:0|max:100000',
            'note' => 'nullable|string|max:255',
        ]);

        $currentBalance = (int) $user->tokens;
        $requestedAmount = (int) $data['tokens'];
        $adminNote = $data['note'] ?? null;

        if (in_array($data['operation'], ['add', 'deduct'], true) && $requestedAmount < 1) {
            return back()->withErrors(['tokens' => 'Token amount must be at least 1 for add or deduct actions.']);
        }

        $delta = match ($data['operation']) {
            'add' => $requestedAmount,
            'deduct' => -$requestedAmount,
            'set' => $requestedAmount - $currentBalance,
        };

        $newBalance = $currentBalance + $delta;
        if ($newBalance < 0) {
            return back()->withErrors(['tokens' => 'Token balance cannot go below zero.']);
        }

        if ($delta === 0) {
            return back()->with('status', 'Token balance already matches the requested amount.');
        }

        $user->update(['tokens' => $newBalance]);
        $user->refresh();

        [$type, $fallbackDescription] = match ($data['operation']) {
            'add' => ['manual_credit', 'Manual token credit by super admin'],
            'deduct' => ['manual_debit', 'Manual token deduction by super admin'],
            'set' => ['manual_settlement', 'Token balance adjusted by super admin'],
        };

        $this->recordTokenEvent(
            $user,
            $delta,
            $type,
            $adminNote ?: $fallbackDescription,
            [
                'adjusted_by' => auth()->id(),
                'operation' => $data['operation'],
                'requested_amount' => $requestedAmount,
                'previous_balance' => $currentBalance,
                'new_balance' => $newBalance,
                'note' => $adminNote,
            ]
        );

        return back()->with('status', 'Token balance updated successfully.');
    }

    public function topupRequests()
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $requests = TokenTopupRequest::with(['user', 'reviewer'])->latest()->get();

        return view('admin.topups', compact('requests'));
    }

    public function approveTopupRequest(Request $request, TokenTopupRequest $topupRequest)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        abort_if($topupRequest->status !== 'pending', 422, 'This request has already been reviewed.');

        $data = $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $topupRequest->user->increment('tokens', $topupRequest->requested_tokens);
        $topupRequest->user->refresh();

        $topupRequest->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_note' => $data['admin_note'] ?? null,
        ]);

        $this->recordTokenEvent(
            $topupRequest->user,
            $topupRequest->requested_tokens,
            'manual_topup_approved',
            'Top-up request approved',
            ['topup_request_id' => $topupRequest->id, 'reviewed_by' => auth()->id()]
        );

        return back()->with('status', 'Top-up request approved successfully.');
    }

    public function rejectTopupRequest(Request $request, TokenTopupRequest $topupRequest)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        abort_if($topupRequest->status !== 'pending', 422, 'This request has already been reviewed.');

        $data = $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $topupRequest->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_note' => $data['admin_note'] ?? null,
        ]);

        return back()->with('status', 'Top-up request rejected.');
    }

    // --- Helpers ---
    private function normalizedAiFieldDefinitions(array $aiFields): array
    {
        $definitions = [];
        foreach ($aiFields as $key => $value) {
            $fieldKey = is_string($key) ? $key : (is_array($value) ? ($value['key'] ?? '') : $value);
            $fieldLabel = is_array($value) ? ($value['label'] ?? $fieldKey) : (is_string($key) ? $value : $fieldKey);

            if (!$fieldKey) {
                continue;
            }

            $definitions[] = [
                'key' => strtolower(preg_replace('/[^a-z0-9]/i', '_', $fieldKey)),
                'label' => $fieldLabel,
                'type' => 'text',
            ];
        }
        return $definitions;
    }

    private function resolvedExportLayoutForTemplate(SlipTemplate $template): array
    {
        return $template->export_layout ?? $template->ai_fields ?? [];
    }

    private function defaultExportLayoutForFields(array $fields): array
    {
        return $fields;
    }

    private function detectTemplateForUser(int $tenantId, string $fullPath): SlipTemplate
    {
        $templates = SlipTemplate::where('merchant_id', $tenantId)->get();
        if ($templates->isEmpty()) {
            throw new \Exception('You have no Profiles to auto-detect against. Please create one first.');
        }

        $templateNames = $templates->pluck('name')->toArray();
        $detectedName = $this->geminiService->identifyStoreFromImage($fullPath, $templateNames);

        if (!$detectedName || $detectedName === 'UNKNOWN') {
            throw new \Exception('Intelligence could not confidently map this receipt to any of your Profiles. Please select manually.');
        }

        $template = $templates->first(function ($candidate) use ($detectedName) {
            return strcasecmp(trim($candidate->name), $detectedName) === 0;
        });

        if (!$template) {
            throw new \Exception("Intelligence mapped to '{$detectedName}' but it does not match your active profiles.");
        }

        return $template;
    }

    private function authorizeMerchant(Merchant $merchant): void
    {
        // Merchant auth is handled by IdentifyTenant middleware
        // But for Central functions (like edit store info), use this fallback:
        if (!app()->bound('tenant')) {
            $user = auth()->user();
            abort_if(!$user->isSuperAdmin() && !$user->merchants()->where('merchant_id', $merchant->id)->exists() && $merchant->user_id !== $user->id, 403);
        }
    }

    private function authorizeMerchantDeletion(Merchant $merchant): void
    {
        $user = auth()->user();
        abort_if(!$user, 403);

        if ($user->isSuperAdmin() || (int) $merchant->user_id === (int) $user->id) {
            return;
        }

        $workspaceMembership = $merchant->users()->where('user_id', $user->id)->first();
        $workspaceRole = $workspaceMembership?->pivot?->role;

        abort_if($workspaceRole !== 'owner', 403, 'Only project owners can delete this workspace.');
    }

    private function authorizeTemplate(SlipTemplate $template): void
    {
        if (app()->bound('tenant')) {
            abort_if($template->merchant_id !== app('tenant')->id, 403);
        }
    }

    private function authorizeSlipBatch(SlipBatch $batch): void
    {
        if (app()->bound('tenant')) {
            abort_if((int) $batch->merchant_id !== (int) app('tenant')->id, 403);
        }
    }

    private function authorizeSlip(Slip $slip): void
    {
        if (app()->bound('tenant')) {
            abort_if($slip->template->merchant_id !== app('tenant')->id, 403);
        }
    }

    private function recordTokenUsage(User $user, Slip $slip, int $delta, string $description, array $meta = []): void
    {
        TokenLog::create([
            'user_id' => $user->id,
            'slip_id' => $slip->id,
            'delta' => $delta,
            'balance_after' => max(0, (int) $user->tokens),
            'type' => 'usage',
            'description' => $description,
            'meta' => $meta,
        ]);
    }

    private function recordTokenEvent(User $user, int $delta, string $type, string $description, array $meta = []): void
    {
        TokenLog::create([
            'user_id' => $user->id,
            'slip_id' => null,
            'delta' => $delta,
            'balance_after' => max(0, (int) $user->tokens),
            'type' => $type,
            'description' => $description,
            'meta' => $meta,
        ]);
    }

    private function resolveWorkspaceAccessLabel(User $user, Merchant $tenant): string
    {
        if ((int) $tenant->user_id === (int) $user->id) {
            return 'Owner';
        }

        $workspaceMembership = $tenant->users()->where('user_id', $user->id)->first();
        $workspaceRole = $workspaceMembership?->pivot?->role;

        return match ($workspaceRole) {
            'owner' => 'Owner',
            'admin' => 'Admin',
            'employee' => 'Employee',
            default => 'Member',
        };
    }

    private function resolveRequestedSubdomain(string $name, ?string $requestedSubdomain = null, ?int $ignoreMerchantId = null): string
    {
        $candidate = Str::of($requestedSubdomain ?: $name)
            ->lower()
            ->slug('-')
            ->substr(0, 63)
            ->trim('-')
            ->value();

        if ($candidate === '') {
            $candidate = 'store';
        }

        $base = $candidate;
        $suffix = 1;

        while (Merchant::query()
            ->when($ignoreMerchantId, fn ($query) => $query->whereKeyNot($ignoreMerchantId))
            ->where('subdomain', $candidate)
            ->exists()) {
            $suffixLabel = '-' . $suffix;
            $candidate = Str::limit($base, 63 - strlen($suffixLabel), '') . $suffixLabel;
            $suffix++;
        }

        return $candidate;
    }
}




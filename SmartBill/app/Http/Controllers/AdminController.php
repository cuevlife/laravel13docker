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

    // --- Store Management ---
    public function stores()
    {
        $stores = Merchant::withCount('templates')->where('user_id', auth()->id())->latest()->get();
        return view('main.stores', compact('stores'));
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
if ($request->confirmation !== $merchant->name) {
    return response()->json([
        'status' => 'error',
        'message' => 'Folder name confirmation does not match.',
    ], 422);
}

if ((int) $request->session()->get('active_folder_id') === (int) $merchant->id) {
    $request->session()->forget('active_folder_id');
}

        $merchant->delete();

        return response()->json(['status' => 'success']);
    }

    // --- Extraction Profiles (Phased Out) ---
    // Users no longer manage their own profiles. Everything uses the Global Prompt.

    // --- Slip Registry ---
    public function slipReader(Request $request)
    {
        $tenant = app('tenant');
        $batches = SlipBatch::where('merchant_id', $tenant->id)
            ->latest('scanned_at')
            ->latest()
            ->get();

        $slipsQuery = $this->baseSlipQueryForTenant($tenant);
        $this->applySlipFilters($slipsQuery, $request);

        $sort = (string) $request->input('sort', 'processed_at_desc');
        switch ($sort) {
            case 'oldest':
            case 'processed_at_asc':
                $slipsQuery->orderBy('processed_at')->orderBy('id');
                break;
            case 'date_asc':
                $slipsQuery->orderByRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(extracted_data, "$.date")) AS DATE) ASC')->orderBy('id');
                break;
            case 'date_desc':
                $slipsQuery->orderByRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(extracted_data, "$.date")) AS DATE) DESC')->orderByDesc('id');
                break;
            case 'amount_asc':
                $slipsQuery->orderByRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(extracted_data, "$.total_amount")) AS DECIMAL(12,2)) ASC')->orderBy('id');
                break;
            case 'amount_desc':
                $slipsQuery->orderByRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(extracted_data, "$.total_amount")) AS DECIMAL(12,2)) DESC')->orderByDesc('id');
                break;
            case 'shop_asc':
                $slipsQuery->orderByRaw('COALESCE(JSON_UNQUOTE(JSON_EXTRACT(extracted_data, "$.shop_name")), JSON_UNQUOTE(JSON_EXTRACT(extracted_data, "$.store_name"))) ASC')->orderBy('id');
                break;
            case 'shop_desc':
                $slipsQuery->orderByRaw('COALESCE(JSON_UNQUOTE(JSON_EXTRACT(extracted_data, "$.shop_name")), JSON_UNQUOTE(JSON_EXTRACT(extracted_data, "$.store_name"))) DESC')->orderByDesc('id');
                break;
            case 'status_asc':
                $slipsQuery->orderBy('workflow_status')->orderBy('id');
                break;
            case 'status_desc':
                $slipsQuery->orderByDesc('workflow_status')->orderByDesc('id');
                break;
            case 'latest':
            case 'processed_at_desc':
            default:
                $slipsQuery->orderByDesc('processed_at')->orderByDesc('id');
                break;
        }

        $slips = $slipsQuery->paginate(50)->withQueryString();
        $slips->getCollection()->transform(fn (Slip $slip) => $this->decorateSlipForDisplay($slip));

        // Folder-specific Export Settings
        $exportColumns = $tenant->config['export_columns'] ?? null;
        if (!$exportColumns) {
            $superAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();
            $exportColumns = $superAdmin?->settings['export_columns'] ?? [
                ['key' => 'processed_at', 'label' => 'Processed Date', 'enabled' => true, 'order' => 1],
                ['key' => 'uid', 'label' => 'Document UID', 'enabled' => true, 'order' => 2],
                ['key' => 'shop_name', 'label' => 'Store Name', 'enabled' => true, 'order' => 3],
                ['key' => 'total_amount', 'label' => 'Total Amount', 'enabled' => true, 'order' => 4],
                ['key' => 'items', 'label' => 'Items (Single Cell)', 'enabled' => false, 'order' => 5],
                ['key' => 'item_name', 'label' => 'Item Name (Split Rows)', 'enabled' => false, 'order' => 6],
                ['key' => 'item_price', 'label' => 'Item Price (Split Rows)', 'enabled' => false, 'order' => 7],
            ];
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => $slips->items(),
                'pagination' => [
                    'current_page' => $slips->currentPage(),
                    'last_page' => $slips->lastPage(),
                    'total' => $slips->total(),
                    'links' => $slips->linkCollection(),
                ],
                'export_columns' => $exportColumns,
            ]);
        }

        $workflowOptions = Slip::workflowOptions();

        $labelSuggestions = collect([]); // Removed label support

        $currentCollection = null;
        $currentCollectionSummary = null;
        $currentCollectionId = (int) $request->input('batch_id', 0);

        if ($currentCollectionId > 0) {
            $currentCollection = $batches->firstWhere('id', $currentCollectionId);

            if ($currentCollection) {
                $currentCollectionSummary = [
                    'total_slips' => $currentCollection->slips()->count(),
                ];
            }
        }

        return view('main.slip', [
            'batches' => $batches,
            'slips' => $slips,
            'workflowOptions' => $workflowOptions,
            'labelSuggestions' => $labelSuggestions,
            'currentCollection' => $currentCollection,
            'currentCollectionSummary' => $currentCollectionSummary,
            'exportColumns' => $exportColumns,
            'activeFilters' => [
                'q' => (string) $request->input('q', ''),
                'date_from' => (string) $request->input('date_from', ''),
                'date_to' => (string) $request->input('date_to', ''),
                'batch_id' => (string) $request->input('batch_id', ''),
                'workflow_status' => (string) $request->input('workflow_status', ''),
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
        
        if ($slip->template) {
            $exportColumns = $slip->template->ai_fields ?? [];
        } else {
            $superAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();
            $settings = $superAdmin?->settings ?: [];
            $exportColumns = $settings['global_ai_fields'] ?? [
                ['key' => 'shop_name', 'label' => 'Shop Name', 'type' => 'text'],
                ['key' => 'date', 'label' => 'Transaction Date', 'type' => 'text'],
                ['key' => 'total_amount', 'label' => 'Total Amount', 'type' => 'text'],
            ];
        }

        return view('main.slip-edit', compact('slip', 'exportColumns'));
    }

    public function processSlip(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image',
            'batch_id' => 'nullable|integer',
            'batch_name' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        \App\Support\ImageOptimizer::optimizeUpload($request->file('image'), 2048, 2048, 90);
        
        // Calculate hash to prevent duplicates
        $imageHash = md5_file($request->file('image')->getRealPath());
        $tenant = app('tenant');

        // Check Folder Capacity Limit
        $tenantSlipsCount = \App\Models\Slip::whereHas('batch', function($q) use ($tenant) {
            $q->where('merchant_id', $tenant->id);
        })->count();

        if ($tenantSlipsCount >= ($tenant->max_slips ?? 10000)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Folder limit reached. Maximum ' . number_format($tenant->max_slips ?? 10000) . ' slips allowed per folder. Please contact an administrator.',
            ], 403);
        }

        $exists = Slip::where('image_hash', $imageHash)
            ->whereHas('batch', function($q) use ($tenant) {
                $q->where('merchant_id', $tenant->id);
            })->exists();

        if ($exists) {
            return response()->json([
                'status' => 'duplicate',
                'message' => 'Duplicate slip detected. This receipt has already been uploaded to this folder.',
            ]);
        }

        $path = $request->file('image')->store('slips', 'public');
        $fullPath = Storage::disk('public')->path($path);

        try {
            // Get Folder Configuration (with Global Fallback)
            $superAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();
            $settings = $superAdmin?->settings ?: [];
            
            $aiFields = $tenant->config['ai_fields'] ?? $settings['global_ai_fields'] ?? [
                ['key' => 'shop_name', 'label' => 'Shop Name', 'type' => 'text'],
                ['key' => 'date', 'label' => 'Transaction Date', 'type' => 'text'],
                ['key' => 'total_amount', 'label' => 'Total Amount', 'type' => 'text'],
            ];
            $geminiModel = $settings['gemini_model'] ?? config('services.gemini.model', 'gemini-1.5-flash');

            $batch = $this->resolveSlipBatch($request, $tenant, $user);

            $data = $this->geminiService->setModel($geminiModel)->extractDataFromImage($fullPath, [
                'ai_fields' => $aiFields,
            ]);

            if (isset($data['status']) && $data['status'] === 'error') {
                throw new \Exception($data['message'] ?? 'AI Extraction failed');
            }

            // Optimize for storage: Shrink the image after AI processing is complete
            \App\Support\ImageOptimizer::optimize($fullPath, 1600, 1600, 75);

            $slip = Slip::create([
                'user_id' => $user->id,
                'slip_template_id' => null, // No longer using specific templates
                'slip_batch_id' => $batch->id,
                'image_path' => $path,
                'image_hash' => $imageHash,
                'extracted_data' => $data,
                'workflow_status' => Slip::WORKFLOW_REVIEWED,
                'processed_at' => now(),
                'reviewed_at' => now(),
            ]);

            $user->decrement('tokens', 1);
            $user->refresh();
            $this->recordTokenUsage($user, $slip, -1, 'Slip scan completed', [
                'batch_id' => $batch->id,
                'batch_name' => $batch->name,
            ]);

            return response()->json([
                'status' => 'success',
                'tokens_remaining' => (int) $user->tokens,
                'batch_name' => $batch->name,
                'collection_name' => $batch->name,
            ]);
        } catch (\RuntimeException $e) {
            Storage::disk('public')->delete($path);
            return response()->json([
                'status' => 'rate_limit',
                'message' => $e->getMessage()
            ], 429);
        } catch (\Exception $e) {
            Storage::disk('public')->delete($path);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function rescanSlip(Request $request, Slip $slip): JsonResponse
    {
        $this->authorizeSlip($slip);
        $user = auth()->user();
        $tenant = app('tenant');
        $extraInstructions = $request->input('instructions');

        if ($user->tokens < 1) {
            return response()->json(['status' => 'error', 'message' => 'Insufficient tokens'], 402);
        }

        try {
            $superAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();
            $settings = $superAdmin?->settings ?: [];
            
            $aiFields = $tenant->config['ai_fields'] ?? $settings['global_ai_fields'] ?? [
                ['key' => 'shop_name', 'label' => 'Shop Name', 'type' => 'text'],
                ['key' => 'date', 'label' => 'Transaction Date', 'type' => 'text'],
                ['key' => 'total_amount', 'label' => 'Total Amount', 'type' => 'text'],
            ];
            $geminiModel = $settings['gemini_model'] ?? config('services.gemini.model', 'gemini-1.5-flash');

            $fullPath = Storage::disk('public')->path($slip->image_path);
            
            $config = ['ai_fields' => $aiFields];
            if ($extraInstructions) {
                $config['extra_instructions'] = $extraInstructions;
            }

            $data = $this->geminiService->setModel($geminiModel)->extractDataFromImage($fullPath, $config);

            if (isset($data['status']) && $data['status'] === 'error') {
                throw new \Exception($data['message'] ?? 'AI Extraction failed');
            }

            $slip->update([
                'extracted_data' => $data,
                'reviewed_at' => now(),
            ]);

            $user->decrement('tokens', 1);
            $this->recordTokenUsage($user, $slip, -1, 'Slip re-scan completed');

            return response()->json([
                'status' => 'success',
                'data' => $data,
                'tokens_remaining' => (int) $user->tokens,
            ]);
        } catch (\Exception $e) {
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
        ]);
        return response()->json(['status' => 'success']);
    }

    public function bulkUpdateSlips(Request $request)
    {
        $tenant = app('tenant');
        $data = $request->validate([
            'slip_ids' => 'required|array|min:1',
            'slip_ids.*' => 'integer',
            'bulk_action' => 'required|string|in:mark_reviewed,mark_approved,mark_exported,delete',
        ]);

        $slipIds = collect($data['slip_ids'])->map(fn ($id) => (int) $id)->unique()->values()->all();
        $slips = $this->baseSlipQueryForTenant($tenant)
            ->whereIn('slips.id', $slipIds)
            ->get();

        if ($slips->isEmpty()) {
            return back()->withErrors(['bulk' => 'Select at least one slip before applying a bulk action.']);
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
                case 'delete':
                    Storage::disk('public')->delete($slip->image_path);
                    $slip->delete();
                    break;
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => count($slipIds) . ' slip(s) updated successfully.'
            ]);
        }

        return back()->with('status', count($slipIds) . ' slip(s) updated successfully.');
    }

    public function updateSlipWorkflow(Request $request, Slip $slip)
    {
        $this->authorizeSlip($slip);
        $allowedStatuses = implode(',', array_keys(Slip::workflowOptions()));
        $data = $request->validate([
            'workflow_status' => 'required|string|in:' . $allowedStatuses,
        ]);

        $slip->update($this->workflowStatusPayload($data['workflow_status']));

        return back()->with('status', 'Slip workflow updated.');
    }

    public function deleteSlip(Slip $slip): JsonResponse
    {
        $this->authorizeSlip($slip);
        Storage::disk('public')->delete($slip->image_path);
        $slip->delete();
        return response()->json(['status' => 'success']);
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

        // --- 1. Load Configuration (Folder-specific with Global Fallback) ---
        $exportConfig = $tenant->config['export_columns'] ?? null;
        
        $superAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();
        $globalSettings = $superAdmin?->settings ?: [];

        if (!$exportConfig) {
            $exportConfig = $globalSettings['export_columns'] ?? [
                ['key' => 'processed_at', 'label' => 'Processed Date', 'enabled' => true, 'order' => 1],
                ['key' => 'uid', 'label' => 'Document UID', 'enabled' => true, 'order' => 2],
                ['key' => 'shop_name', 'label' => 'Store Name', 'enabled' => true, 'order' => 3],
                ['key' => 'total_amount', 'label' => 'Total Amount', 'enabled' => true, 'order' => 4],
            ];
        }

        $vendorMaps = collect($globalSettings['vendor_mapping'] ?? [])->pluck('code', 'text')->toArray();
        $itemMaps = collect($globalSettings['item_mapping'] ?? [])->pluck('code', 'text')->toArray();
        
        $excelFilename = $tenant->config['excel_filename'] ?? ($tenant->name . '_Export_' . now()->format('Ymd') . '.xlsx');
        if (!Str::endsWith($excelFilename, '.xlsx')) {
            $excelFilename .= '.xlsx';
        }

        // Filter and sort active columns
        $activeCols = collect($exportConfig)
            ->filter(fn($c) => !empty($c['enabled']))
            ->sortBy('order')
            ->values();

        if ($activeCols->isEmpty()) {
            return redirect()->to(WorkspaceUrl::current(request(), 'slips'))->with('error', 'Excel Export Designer has no enabled columns.');
        }

        $headings = $activeCols->pluck('label')->toArray();
        $rows = [];

        // --- 2. Process Data Rows ---
        foreach ($filteredSlips as $slip) {
            $data = $slip->extracted_data ?: [];
            $itemsRaw = $data['items'] ?? [['name' => '-', 'price' => 0]];
            $items = is_array($itemsRaw) ? $itemsRaw : [$itemsRaw];

            // Expand to multiple rows only if itemized columns are present
            $hasItemizedCols = $activeCols->contains(fn($c) => in_array($c['key'], ['item_name', 'item_code', 'item_price']));

            if ($hasItemizedCols) {
                foreach ($items as $item) {
                    $row = [];
                    foreach ($activeCols as $col) {
                        $row[] = $this->resolveExportValue($col['key'], $slip, $data, $item, $vendorMaps, $itemMaps);
                    }
                    $rows[] = $row;
                }
            } else {
                $row = [];
                foreach ($activeCols as $col) {
                    $row[] = $this->resolveExportValue($col['key'], $slip, $data, null, $vendorMaps, $itemMaps);
                }
                $rows[] = $row;
            }
        }

        // --- 3. Audit & Status Update ---
        $filteredSlips->each(fn($s) => $s->update($this->workflowStatusPayload(Slip::WORKFLOW_EXPORTED)));

        \App\Models\SlipExport::create([
            'user_id' => auth()->id(),
            'merchant_id' => $tenant->id,
            'file_name' => $excelFilename,
            'file_format' => 'xlsx',
            'export_mode' => $hasItemizedCols ? 'granular' : 'summary',
            'slips_count' => $filteredSlips->count(),
            'filters' => $request->all(),
            'exported_at' => now(),
        ]);

        return Excel::download(
            new SlipWorkbookExport([
                [
                    'title' => 'Registry Export',
                    'headings' => $headings,
                    'rows' => $rows
                ]
            ]),
            $excelFilename
        );
    }

    public function updateExportSettings(Request $request)
    {
        $tenant = app('tenant');
        $data = $request->validate([
            'export_columns' => 'required|array',
            'excel_filename' => 'nullable|string|max:255',
        ]);

        $config = $tenant->config ?: [];
        $config['export_columns'] = $data['export_columns'];
        $config['excel_filename'] = $data['excel_filename'] ?? null;
        $tenant->update(['config' => $config]);

        return response()->json(['status' => 'success']);
    }

    public function exportHistory(Request $request)
    {
        $tenant = app('tenant');
        $exports = \App\Models\SlipExport::where('merchant_id', $tenant->id)
            ->with('user')
            ->latest('exported_at')
            ->limit(20)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $exports->map(fn($e) => [
                'id' => $e->id,
                'file_name' => $e->file_name,
                'slips_count' => $e->slips_count,
                'user_name' => $e->user->name ?? 'System',
                'exported_at' => $e->exported_at->format('d M Y H:i'),
                'mode' => $e->export_mode,
            ])
        ]);
    }

    private function resolveExportValue(string $key, Slip $slip, array $data, $item, array $vendorMaps, array $itemMaps)
    {
        switch ($key) {
            case 'processed_at':
                return optional($slip->processed_at)->format('Y-m-d H:i:s');
            case 'uid':
                return $slip->uid;
            case 'shop_name':
                return $data['shop_name'] ?? $data['store_name'] ?? '';
            case 'vendor_code':
                $shopName = trim($data['shop_name'] ?? $data['store_name'] ?? '');
                return $vendorMaps[$shopName] ?? '';
            case 'item_name':
                return is_array($item) 
                    ? ($item['name'] ?? $item['item_name'] ?? $item['item'] ?? $item['description'] ?? $item['desc'] ?? '-')
                    : (is_string($item) ? $item : '-');
            case 'item_code':
                $itemName = is_array($item) 
                    ? trim($item['name'] ?? $item['item_name'] ?? $item['item'] ?? $item['description'] ?? $item['desc'] ?? '')
                    : (is_string($item) ? trim($item) : '');
                return $itemMaps[$itemName] ?? '';
            case 'item_price':
                return is_array($item)
                    ? ($item['price'] ?? $item['amount'] ?? $item['total'] ?? 0)
                    : 0;
            default:
                $val = $data[$key] ?? '';
                if (is_array($val)) {
                    return $this->formatArrayForExcel($val);
                }
                return $val;
        }
    }

    private function formatArrayForExcel(array $arr): string
    {
        return collect($arr)->map(function($i) {
            if (is_array($i)) {
                $parts = [];
                $name = $i['name'] ?? $i['item'] ?? $i['description'] ?? $i['desc'] ?? null;
                $qty = $i['qty'] ?? $i['quantity'] ?? $i['amount'] ?? null;
                $price = $i['price'] ?? $i['total'] ?? null;
                
                if ($qty) $parts[] = $qty . 'x';
                if ($name) $parts[] = $name;
                if ($price) $parts[] = '(' . $price . ')';
                
                return empty($parts) ? json_encode($i, JSON_UNESCAPED_UNICODE) : implode(' ', $parts);
            }
            return (string) $i;
        })->implode("\n");
    }

    private function baseSlipQueryForTenant(Merchant $tenant): Builder
    {
        return Slip::query()
            ->with(['template.merchant', 'batch'])
            ->where(function (Builder $query) use ($tenant) {
                $query->whereHas('batch', function (Builder $q) use ($tenant) {
                    $q->where('merchant_id', $tenant->id);
                })
                ->orWhereHas('template', function (Builder $q) use ($tenant) {
                    $q->where('merchant_id', $tenant->id);
                });
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
            $keywords = collect(preg_split('/\s+/', $term) ?: [])
                ->map(fn ($keyword) => trim((string) $keyword))
                ->filter()
                ->unique()
                ->values();

            $query->where(function (Builder $searchQuery) use ($term, $keywords) {
                $allTerms = $keywords->isNotEmpty() ? $keywords->all() : [$term];

                foreach ($allTerms as $keyword) {
                    $searchQuery->where(function (Builder $nestedQuery) use ($keyword) {
                        $nestedQuery
                            ->where('slips.id', 'like', '%' . $keyword . '%')
                            ->orWhere('slips.uid', 'like', '%' . $keyword . '%')
                            ->orWhere('slips.image_path', 'like', '%' . $keyword . '%')
                            ->orWhere('slips.workflow_status', 'like', '%' . $keyword . '%')
                            ->orWhere('slips.processed_at', 'like', '%' . $keyword . '%')
                            ->orWhere('slips.reviewed_at', 'like', '%' . $keyword . '%')
                            ->orWhere('slips.approved_at', 'like', '%' . $keyword . '%')
                            ->orWhere('slips.exported_at', 'like', '%' . $keyword . '%')
                            ->orWhere('slips.extracted_data', 'like', '%' . $keyword . '%')
                            ->orWhere('slips.labels', 'like', '%' . $keyword . '%')
                            ->orWhereHas('template', function (Builder $templateQuery) use ($keyword) {
                                $templateQuery->where('name', 'like', '%' . $keyword . '%')
                                    ->orWhereHas('merchant', function (Builder $merchantQuery) use ($keyword) {
                                        $merchantQuery
                                            ->where('name', 'like', '%' . $keyword . '%')
                                            ->orWhere('address', 'like', '%' . $keyword . '%')
                                            ->orWhere('tax_id', 'like', '%' . $keyword . '%')
                                            ->orWhere('phone', 'like', '%' . $keyword . '%');
                                    });
                            })
                            ->orWhereHas('batch', function (Builder $batchQuery) use ($keyword) {
                                $batchQuery
                                    ->where('name', 'like', '%' . $keyword . '%')
                                    ->orWhere('note', 'like', '%' . $keyword . '%');
                            })
                            ->orWhereHas('user', function (Builder $userQuery) use ($keyword) {
                                $userQuery
                                    ->where('name', 'like', '%' . $keyword . '%')
                                    ->orWhere('username', 'like', '%' . $keyword . '%')
                                    ->orWhere('email', 'like', '%' . $keyword . '%');
                            });
                    });
                }
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
    }

    private function decorateSlipForDisplay(Slip $slip): Slip
    {
        $data = $slip->extracted_data ?: [];
        $slip->display_shop = $data['shop_name'] ?? $data['store_name'] ?? $slip->template?->merchant?->name ?? 'Unknown';
        
        // Ensure date is formatted as d/m/Y for table display
        $rawDate = $data['date'] ?? $data['transaction_date'] ?? null;
        if ($rawDate) {
            try {
                $slip->display_date = \Carbon\Carbon::parse($rawDate)->format('d/m/Y');
            } catch (\Exception $e) {
                $slip->display_date = $rawDate; // Fallback to raw if unparseable
            }
        } else {
            $slip->display_date = optional($slip->processed_at)->format('d/m/Y') ?? '-';
        }
        
        $rawAmount = $data['total_amount'] ?? $data['total'] ?? $data['final_total'] ?? 0;
        $slip->display_amount = is_numeric($rawAmount) ? $rawAmount : (preg_replace('/[^0-9.]/', '', (string) $rawAmount) ?: 0);
        
        $slip->batch_name = $slip->batch?->name ?? 'Inbox';
        $slip->display_labels = $this->normalizeSlipLabels($slip->labels ?? []);

        return $slip;
    }

    private function workflowStatusPayload(string $workflowStatus): array
    {
        $updates = [
            'workflow_status' => $workflowStatus,
        ];

        if ($workflowStatus === Slip::WORKFLOW_REVIEWED) {
            $updates['reviewed_at'] = now();
        }

        if ($workflowStatus === Slip::WORKFLOW_APPROVED) {
            $updates['approved_at'] = now();
        }

        if ($workflowStatus === Slip::WORKFLOW_EXPORTED) {
            $updates['exported_at'] = now();
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
        \App\Support\ImageOptimizer::optimizeUpload($file, 3072, 3072, 95);
        $path = $file->store('temp', 'public');
        $fullPath = Storage::disk('public')->path($path);

        try {
            $superAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();
            $settings = $superAdmin?->settings ?: [];
            $geminiModel = $settings['gemini_model'] ?? config('services.gemini.model', 'gemini-1.5-flash');

            $raw = $this->geminiService->setModel($geminiModel)->suggestSchemaFromImage($fullPath);
            $fields = $this->normalizedAiFieldDefinitions($raw);
            return response()->json(['status' => 'success', 'ai_fields' => $fields]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Suggest Schema Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'AI Analysis failed: ' . $e->getMessage()
            ], 500);
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
        ];

        return view('main.users', compact('users', 'stats', 'merchants'));
    }

    public function createUser()
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        return view('main.user-create');
    }

    public function superAdminDashboard()
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        
        // Super Admin doesn't need a summary dashboard, they need to manage data.
        return redirect()->to(\App\Support\OwnerUrl::path(request(), 'users'));
    }

    public function folders()
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $merchants = Merchant::with(['owner', 'users'])
            ->withCount(['templates', 'slips'])
            ->latest()
            ->get();

        $candidateOwners = User::query()->orderBy('name')->get();

        $stats = [
            'folders' => $merchants->count(),
            'archived' => $merchants->where('status', 'archived')->count(),
            'memberships' => (int) $merchants->sum(fn (Merchant $merchant) => $merchant->users->count()),
            'templates' => (int) $merchants->sum('templates_count'),
            'slips' => (int) $merchants->sum('slips_count'),
        ];

        return view('main.folders', compact('merchants', 'candidateOwners', 'stats'));
    }

    public function createFolder()
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        $candidateOwners = User::query()->orderBy('name')->get();
        return view('main.folder-create', compact('candidateOwners'));
    }

    public function storeFolderForAdmin(Request $request)
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

        return back()->with('status', 'Folder created successfully.');
    }

    public function showFolder(Merchant $merchant)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $merchant->load([
            'owner',
            'users' => fn ($query) => $query->orderBy('name'),
            'templates',
        ])->loadCount(['templates', 'slips']);

        $candidateUsers = User::query()->orderBy('name')->get();
        $recentSlips = Slip::with(['user', 'template'])
            ->where(function ($query) use ($merchant) {
                $query->whereHas('template', function ($q) use ($merchant) {
                    $q->where('merchant_id', $merchant->id);
                })
                ->orWhereHas('batch', function ($q) use ($merchant) {
                    $q->where('merchant_id', $merchant->id);
                });
            })
            ->latest('processed_at')
            ->limit(12)
            ->get();

        // AI Schema Designer Data
        $schemaFields = $merchant->config['ai_fields'] ?? null;
        if (!$schemaFields) {
            $superAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();
            $schemaFields = $superAdmin?->settings['global_ai_fields'] ?? [
                ['key' => 'shop_name', 'label' => 'ชื่อร้าน', 'type' => 'text', 'hint' => ''],
                ['key' => 'date', 'label' => 'วันที่', 'type' => 'date', 'hint' => ''],
                ['key' => 'total_amount', 'label' => 'ยอดรวม', 'type' => 'number', 'hint' => ''],
                ['key' => 'items', 'label' => 'รายการสินค้า', 'type' => 'array', 'hint' => ''],
            ];
        }

        return view('main.folder-detail', compact('merchant', 'candidateUsers', 'recentSlips', 'schemaFields'));
    }

    public function updateFolderForAdmin(Request $request, Merchant $merchant)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'max_slips' => 'required|integer|min:1|max:1000000',
            'ai_fields' => 'nullable|array',
        ]);

        $previousOwnerId = $merchant->user_id;

        $config = $merchant->config ?: [];
        if (isset($data['ai_fields'])) {
            $config['ai_fields'] = $data['ai_fields'];
        }

        $merchant->update([
            'name' => $data['name'],
            'subdomain' => $this->resolveRequestedSubdomain($data['name'], null, $merchant->id),
            'user_id' => $data['user_id'] ?? null,
            'address' => $data['address'] ?? null,
            'tax_id' => $data['tax_id'] ?? null,
            'phone' => $data['phone'] ?? null,
            'max_slips' => (int) $data['max_slips'],
            'config' => $config,
        ]);

        if (!empty($data['user_id'])) {
            $merchant->users()->syncWithoutDetaching([
                (int) $data['user_id'] => ['role' => 'owner'],
            ]);

            if ($previousOwnerId && (int) $previousOwnerId !== (int) $data['user_id']) {
                $merchant->users()->updateExistingPivot($previousOwnerId, ['role' => 'admin']);
            }
        }

        return back()->with('status', 'Folder updated successfully.');
    }

    public function updateFolderStatus(Request $request, Merchant $merchant)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $data = $request->validate([
            'status' => 'required|string|in:active,archived',
        ]);

        $merchant->update([
            'status' => $data['status'],
        ]);

        return back()->with('status', 'Folder status updated successfully.');
    }

    public function attachFolderMember(Request $request, Merchant $merchant)
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

        return back()->with('status', 'Folder member added successfully.');
    }

    public function updateFolderMemberRole(Request $request, Merchant $merchant, User $user)
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

        return back()->with('status', 'Folder member role updated successfully.');
    }

    public function detachFolderMember(Merchant $merchant, User $user)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        if ((int) $merchant->user_id === (int) $user->id) {
            return back()->withErrors(['folder' => 'Primary owner cannot be removed from the folder.']);
        }

        $merchant->users()->detach($user->id);

        return back()->with('status', 'Folder member removed successfully.');
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

        return view('main.user-detail', compact(
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
            return back()->withErrors(['user' => 'This user owns one or more folders. Reassign those folders before deleting the account.']);
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
            'max_folders' => 'required|integer|min:1|max:100',
        ]);

        $user->update([
            'role' => (int) $data['role'],
            'max_folders' => (int) $data['max_folders'],
        ]);

        return back()->with('status', 'User access and limits updated successfully.');
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

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Token balance updated successfully.',
                'redirect' => \App\Support\OwnerUrl::path($request, 'users')
            ]);
        }

        return back()->with('status', 'Token balance updated successfully.');
    }

    public function systemSettings()
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        $user = auth()->user();
        $settings = $user->settings ?: [];

        // --- AI Defaults ---
        if (empty($settings['global_ai_fields'])) {
            $settings['global_ai_fields'] = [
                ['key' => 'shop_name', 'label' => 'ชื่อร้าน', 'type' => 'text', 'hint' => ''],
                ['key' => 'date', 'label' => 'วันที่', 'type' => 'date', 'hint' => ''],
                ['key' => 'total_amount', 'label' => 'ยอดรวม', 'type' => 'number', 'hint' => ''],
                ['key' => 'items', 'label' => 'รายการสินค้า', 'type' => 'array', 'hint' => ''],
            ];
        }

        // Always pass as JSON string for the editor
        $viewSettings = $settings;
        $viewSettings['global_ai_fields'] = json_encode($settings['global_ai_fields'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Fetch usage counts for keys
        $apiKeys = $settings['gemini_api_keys'] ?? [];
        $keyUsage = [];
        foreach ($apiKeys as $key) {
            $keyUsage[$key] = (int) \Illuminate\Support\Facades\Cache::get('gemini_key_usage_' . md5($key), 0);
        }
        $viewSettings['api_key_usage'] = $keyUsage;

        return view('main.admin-settings', ['settings' => $viewSettings]);
    }

    public function updateSystemSettings(Request $request)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        $data = $request->validate([
            'gemini_model' => 'required|string|max:50',
            'api_keys' => 'nullable|array',
        ]);

        $user = auth()->user();
        $settings = $user->settings ?: [];
        
        $settings['gemini_model'] = trim($data['gemini_model']);
        $settings['gemini_api_keys'] = array_values(array_filter(array_map('trim', $data['api_keys'] ?? [])));

        $user->update(['settings' => $settings]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success']);
        }

        return back()->with('status', 'System settings updated successfully.');
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
        
        // If no templates exist, let's create one automatically based on intelligence presets
        if ($templates->isEmpty()) {
            $presets = \App\Support\IntelligencePresets::all();
            $presetKeys = array_keys($presets);
            
            // Ask AI to identify which preset fits best
            $detectedType = $this->geminiService->identifyStoreFromImage($fullPath, $presetKeys) ?: 'retail';
            if (!isset($presets[$detectedType])) {
                $detectedType = 'retail';
            }
            
            $preset = $presets[$detectedType];
            
            // Create the first profile for this folder automatically
            return SlipTemplate::create([
                'merchant_id' => $tenantId,
                'user_id' => auth()->id(),
                'name' => "Auto: " . $preset['name'],
                'main_instruction' => $preset['main_instruction'],
                'ai_fields' => $preset['ai_fields'],
            ]);
        }

        $templateNames = $templates->pluck('name')->toArray();
        $detectedName = $this->geminiService->identifyStoreFromImage($fullPath, $templateNames);

        if (!$detectedName || $detectedName === 'UNKNOWN') {
            // Fallback to the first available template if AI is unsure
            return $templates->first();
        }

        $template = $templates->first(function ($candidate) use ($detectedName) {
            return strcasecmp(trim($candidate->name), $detectedName) === 0;
        });

        if (!$template) {
            return $templates->first();
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

        abort_if($workspaceRole !== 'owner', 403, 'Only folder owners can delete this workspace.');
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
            $merchantId = null;

            if ($slip->template) {
                $merchantId = (int) $slip->template->merchant_id;
            } elseif ($slip->batch) {
                $merchantId = (int) $slip->batch->merchant_id;
            }

            // If neither is found, we should probably check if it belongs to the tenant at all.
            // But if it's orphaned, it's definitely not authorized.
            abort_if($merchantId !== (int) app('tenant')->id, 403);
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

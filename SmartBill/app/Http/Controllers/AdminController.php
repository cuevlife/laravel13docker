<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Merchant;
use App\Models\Slip;
use App\Models\TokenLog;
use App\Models\User;
use App\Models\SystemConfig;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SlipWorkbookExport;

class AdminController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function dashboard()
    {
        return redirect()->route('admin.users');
    }

    public function slipReader(Request $request)
    {
        $tenant = app('tenant');
        $slipsQuery = Slip::where('merchant_id', $tenant->id);
        
        if ($request->filled('q')) {
            $q = $request->input('q');
            $slipsQuery->where(function($query) use ($q) {
                $query->where('uid', 'like', "%{$q}%")
                      ->orWhere('extracted_data', 'like', "%{$q}%");
            });
        }

        if ($request->filled('workflow_status')) {
            $slipsQuery->where('workflow_status', $request->input('workflow_status'));
        }

        $sort = $request->input('sort', 'created_at_desc');
        switch ($sort) {
            case 'created_at_asc': $slipsQuery->orderBy('created_at', 'asc'); break;
            case 'created_at_desc': $slipsQuery->orderBy('created_at', 'desc'); break;
            case 'amount_asc': $slipsQuery->orderBy('amount', 'asc'); break;
            case 'amount_desc': $slipsQuery->orderBy('amount', 'desc'); break;
            default: $slipsQuery->orderBy('created_at', 'desc');
        }

        $slips = $slipsQuery->paginate($request->input('per_page', 20))->withQueryString();
        $slips->getCollection()->transform(fn($s) => $this->decorateSlipForDisplay($s));

        $settings = $this->getSystemSettings();
        $exportColumns = collect($tenant->config['active_cols'] ?? [])->sortBy('order')->values()->toArray();

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $slips->items(),
                'pagination' => [
                    'current_page' => $slips->currentPage(),
                    'last_page' => $slips->lastPage(),
                    'total' => $slips->total(),
                ]
            ]);
        }

        return view('main.slip', [
            'slips' => $slips,
            'tenant' => $tenant,
            'exportColumns' => $exportColumns,
            'activeFilters' => $request->all()
        ]);
    }

    public function processSlip(Request $request): JsonResponse
    {
        $request->validate(['image' => 'required|image']);
        $user = auth()->user(); $tenant = app('tenant');
        if ($user->tokens < 1) return response()->json(['status' => 'error', 'message' => 'Insufficient tokens'], 402);

        $imageHash = md5_file($request->file('image')->getRealPath());
        if (Slip::where('image_hash', $imageHash)->where('merchant_id', $tenant->id)->exists()) {
            return response()->json(['status' => 'duplicate', 'message' => 'Duplicate slip detected.']);
        }

        $path = $request->file('image')->store('slips', 'public');
        try {
            $settings = $this->getSystemSettings();
            $aiFields = $tenant->config['ai_fields'] ?? $settings['default_fields'] ?? [];
            $data = $this->geminiService->extractDataFromImage(Storage::disk('public')->path($path), ['ai_fields' => $aiFields]);

            $slip = Slip::create([
                'uid' => 'SLP-' . strtoupper(substr(uniqid(), 7)),
                'user_id' => $user->id,
                'merchant_id' => $tenant->id,
                'image_path' => $path,
                'image_hash' => $imageHash,
                'extracted_data' => $data,
                'amount' => $data['total_amount'] ?? 0,
                'workflow_status' => Slip::WORKFLOW_REVIEWED,
            ]);

            $user->decrement('tokens', 1);
            $this->recordTokenUsage($user, $slip, -1, 'Slip auto-scan');

            return response()->json(['status' => 'success', 'tokens_remaining' => (int) $user->tokens]);
        } catch (\Exception $e) {
            Storage::disk('public')->delete($path);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

        public function updateExportSettings(Request $request)
    {
        $tenant = app("tenant");
        $data = $request->validate([
            "export_columns" => "required|array",
            "excel_filename" => "nullable|string|max:255"
        ]);

        $config = $tenant->config ?: [];
        // จัดระเบียบใหม่: บังคับลำดับ 1, 2, 3... และล้างคีย์ที่แปลกปลอม
        $config["active_cols"] = collect($data["export_columns"])
            ->map(fn($c, $i) => [
                "key" => $c["key"],
                "label" => $c["label"] ?? $c["key"],
                "enabled" => (bool)($c["enabled"] ?? true),
                "order" => $i + 1
            ])->values()->toArray();
            
        $config["excel_filename"] = $data["excel_filename"];

        $tenant->update(["config" => $config]);
        return response()->json(["status" => "success"]);
    }

    public function updateSystemSettings(Request $request)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        $data = $request->validate([
            'gemini_model' => 'sometimes|string',
            'api_keys' => 'nullable|array',
            'global_ai_fields' => 'nullable|string',
            'excel_export_style' => 'nullable|string',
            'default_fields' => 'nullable|array'
        ]);

        if ($request->has('gemini_model')) SystemConfig::updateOrCreate(['config_key' => 'gemini_model'], ['config_value' => $data['gemini_model']]);
        if ($request->has('api_keys')) {
            $keys = array_values(array_unique(array_filter($data['api_keys'])));
            SystemConfig::updateOrCreate(['config_key' => 'gemini_api_keys'], ['config_value' => json_encode($keys)]);
        }
        if ($request->has('excel_export_style')) SystemConfig::updateOrCreate(['config_key' => 'excel_export_style'], ['config_value' => $data['excel_export_style']]);
        if ($request->has('default_fields')) SystemConfig::updateOrCreate(['config_key' => 'default_fields'], ['config_value' => json_encode($data['default_fields'], JSON_UNESCAPED_UNICODE)]);
        
        return response()->json(['status' => 'success']);
    }

    public function exportExcel(Request $request)
    {
        $tenant = app('tenant');
        $settings = $this->getSystemSettings();
        $exportStyle = $settings['excel_export_style'] ?? 'flat';
        
        $slips = Slip::where('merchant_id', $tenant->id)->latest()->get();
        $activeCols = collect($tenant->config['active_cols'] ?? $settings['default_fields'] ?? [])->filter(fn($c) => !empty($c['enabled']))->sortBy('order');

        if ($exportStyle === 'master_detail') {
            $masterHeadings = $activeCols->filter(fn($c) => $c['key'] !== 'items')->pluck('label')->map(fn($l) => __($l))->toArray();
            $masterRows = [];
            $detailHeadings = [__('Ref UID'), __('Item Name'), __('Item Qty'), __('Item Price'), __('Item Total')];
            $detailRows = [];

            foreach ($slips as $slip) {
                $data = $slip->extracted_data ?: [];
                $mRow = [];
                foreach ($activeCols as $col) {
                    if ($col['key'] === 'items') continue;
                    $val = ($col['key'] === 'created_at') ? $slip->created_at->format('Y-m-d H:i') : ($data[$col['key']] ?? '-');
                    $mRow[] = is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : $val;
                }
                $masterRows[] = $mRow;
                foreach (($data['items'] ?? []) as $item) {
                    $detailRows[] = [$slip->uid, $item['name'] ?? '-', $item['qty'] ?? 1, $item['unit_price'] ?? 0, $item['total_price'] ?? 0];
                }
            }
            $sheets = [['title' => __('Summary'), 'headings' => $masterHeadings, 'rows' => $masterRows], ['title' => __('Items Detail'), 'headings' => $detailHeadings, 'rows' => $detailRows]];
        } else {
            $headings = $activeCols->pluck('label')->map(fn($l) => __($l))->toArray();
            $rows = [];
            foreach ($slips as $slip) {
                $data = $slip->extracted_data ?: [];
                $row = [];
                foreach ($activeCols as $col) {
                    if ($col['key'] === 'items') {
                        $items = $data['items'] ?? [];
                        $itemStrings = [];
                        foreach ($items as $idx => $item) {
                            $itemStrings[] = ($idx + 1) . ". " . ($item['name'] ?? 'Item') . " (" . ($item['qty'] ?? 1) . " x " . number_format($item['unit_price'] ?? 0, 2) . ")";
                        }
                        $row[] = implode("\n", $itemStrings);
                    } else {
                        $val = ($col['key'] === 'created_at') ? $slip->created_at->format('Y-m-d H:i') : ($data[$col['key']] ?? '-');
                        $row[] = is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : $val;
                    }
                }
                $rows[] = $row;
            }
            $sheets = [['title' => __('Export'), 'headings' => $headings, 'rows' => $rows]];
        }

        $filename = ($tenant->config['excel_filename'] ?? 'Export') . '.xlsx';
        return Excel::download(new SlipWorkbookExport($sheets), $filename);
    }

    public function deleteSlip(Slip $slip)
    {
        if (app()->bound('tenant')) abort_if((int)$slip->merchant_id !== (int) app('tenant')->id, 403);
        else abort_unless(auth()->user()->isSuperAdmin(), 403);

        if ($slip->image_path) Storage::disk('public')->delete($slip->image_path);
        $slip->delete();
        return response()->json(['status' => 'success', 'message' => 'Deleted']);
    }

    public function bulkUpdateSlips(Request $request)
    {
        $data = $request->validate(['slip_ids' => 'required|array', 'bulk_action' => 'required|string|in:delete,approve']);
        $slips = Slip::whereIn('id', $data['slip_ids'])->get();
        foreach ($slips as $slip) {
            if (app()->bound('tenant') && (int)$slip->merchant_id !== (int) app('tenant')->id) continue;
            if ($data['bulk_action'] === 'delete') {
                if ($slip->image_path) Storage::disk('public')->delete($slip->image_path);
                $slip->delete();
            } else { $slip->update(['workflow_status' => 'approved']); }
        }
        return response()->json(['status' => 'success']);
    }

    public function systemSettings()
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        $settings = $this->getSystemSettings();
        return view('main.admin-settings', ['settings' => [
            'gemini_model' => $settings['gemini_model'] ?? 'gemini-1.5-flash',
            'gemini_api_keys' => $settings['gemini_api_keys'] ?? [],
            'excel_export_style' => $settings['excel_export_style'] ?? 'flat',
            'default_fields' => $settings['default_fields'] ?? [],
        ]]);
    }

    public function users(Request $request)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        $users = User::withCount(['slips', 'merchants'])->latest()->get();
        if ($request->ajax()) return response()->json(['status' => 'success', 'users' => $users]);
        return view('main.users', ['users' => $users, 'stats' => ['users' => $users->count(), 'slips' => Slip::count()]]);
    }

    public function showUser(User $user)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        $tokenLogs = $user->tokenLogs()->latest()->limit(25)->get();
        $workspaceSnapshots = Merchant::where('user_id', $user->id)->get()->each(fn($m) => $m->slips_count = $m->slips()->count());
        return view('main.user-detail', compact('user', 'tokenLogs', 'workspaceSnapshots'));
    }

    public function storeFolderForAdmin(Request $request)
    {
        $data = $request->validate(['name' => 'required|string', 'user_id' => 'required|exists:users,id']);
        $defaults = json_decode(SystemConfig::where('config_key', 'default_fields')->value('config_value'), true) ?: [];
        Merchant::create(['name' => $data['name'], 'user_id' => $data['user_id'], 'status' => 'active', 'config' => ['ai_fields' => $defaults]]);
        return response()->json(['status' => 'success']);
    }

    public function showFolder(Merchant $merchant)
    {
        $merchant->load(['owner'])->loadCount(['slips']);
        $recentSlips = Slip::where('merchant_id', $merchant->id)->latest()->limit(10)->get();
        $settings = $this->getSystemSettings();
        $schemaFields = $merchant->config['ai_fields'] ?? $settings['default_fields'] ?? [];
        return view('main.folder-detail', ['merchant' => $merchant, 'recentSlips' => $recentSlips, 'schemaFields' => $schemaFields]);
    }

    public function updateFolderForAdmin(Request $request, Merchant $merchant)
    {
        $data = $request->validate(['name' => 'required|string', 'max_slips' => 'required|integer', 'ai_fields' => 'nullable|array']);
        $config = $merchant->config ?: [];
        $config['ai_fields'] = $data['ai_fields'] ?? [];
        $merchant->update(['name' => $data['name'], 'max_slips' => $data['max_slips'], 'config' => $config]);
        return response()->json(['status' => 'success']);
    }

    public function destroyFolder(Merchant $merchant)
    {
        $userId = $merchant->user_id;
        $merchant->delete();
        return redirect()->route('admin.users.show', ['user' => $userId]);
    }

    public function updateFolderStatus(Request $request, Merchant $merchant)
    {
        $merchant->update(['status' => $request->input('status')]);
        return back();
    }

    public function auditLogs()
    {
        return view('main.audit-logs', ['auditLogs' => AuditLog::with('user')->latest()->limit(100)->get(), 'tokenLogs' => TokenLog::with('user')->latest()->limit(100)->get()]);
    }

    private function getSystemSettings(): array
    {
        return SystemConfig::all()->pluck('config_value', 'config_key')->map(fn($v) => json_decode($v, true) ?: $v)->toArray();
    }

    private function decorateSlipForDisplay(Slip $slip): Slip
    {
        $data = $slip->extracted_data ?: [];
        $slip->display_shop = $data['shop_name'] ?? 'Unknown';
        $slip->display_amount = $data['total_amount'] ?? 0;
        return $slip;
    }

    private function recordTokenUsage(User $user, ?Slip $slip, int $delta, string $desc): void
    {
        TokenLog::create(['user_id' => $user->id, 'slip_id' => $slip?->id, 'delta' => $delta, 'balance_after' => $user->tokens, 'type' => 'usage', 'description' => $desc]);
    }
}
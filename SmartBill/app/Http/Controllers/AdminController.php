<?php

namespace App\Http\Controllers;

use App\Exports\SlipWorkbookExport;
use App\Models\SlipExport;
use App\Models\SlipTemplate;
use App\Models\Slip;
use App\Models\Merchant;
use App\Models\User;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService) {
        $this->geminiService = $geminiService;
    }

    public function dashboard() {
        $stats = [
            'stores_count' => Merchant::where('user_id', auth()->id())->count(),
            'templates_count' => SlipTemplate::where('user_id', auth()->id())->count(),
            'slips_count' => Slip::where('user_id', auth()->id())->count(),
            'users_count' => auth()->user()->isAdmin() ? User::count() : 0,
        ];
        return view('admin.dashboard', compact('stats'));
    }

    // --- Store Management ---
    public function stores() {
        $stores = Merchant::withCount('templates')->where('user_id', auth()->id())->latest()->get();
        return view('admin.stores', compact('stores'));
    }

    public function storeStore(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
        ]);
        $data['user_id'] = auth()->id();
        $store = Merchant::create($data);
        return response()->json(['status' => 'success', 'store' => $store]);
    }

    public function updateStore(Request $request, Merchant $merchant) {
        if ($merchant->user_id !== auth()->id()) abort(403);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
        ]);
        $merchant->update($data);
        return response()->json(['status' => 'success']);
    }

    public function deleteStore(Merchant $merchant) {
        if ($merchant->user_id !== auth()->id()) abort(403);
        $merchant->delete();
        return response()->json(['status' => 'success']);
    }

    // --- Extraction Profiles (Templates) ---
    public function merchants() {
        $templates = SlipTemplate::with('merchant')->where('user_id', auth()->id())->latest()->get();
        $stores = Merchant::where('user_id', auth()->id())->orderBy('name')->get();
        return view('admin.templates', compact('templates', 'stores'));
    }

    public function storeMerchant(Request $request) {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'name' => 'required|string|max:255',
        ]);

        $aiFields = $this->normalizedAiFieldDefinitions(['date' => 'วันที่', 'total' => 'ยอดรวม']);
        
        $template = SlipTemplate::create([
            'user_id' => auth()->id(),
            'merchant_id' => $request->merchant_id,
            'name' => $request->name,
            'ai_fields' => $aiFields,
            'main_instruction' => 'Extract data accurately.',
            'export_layout' => $this->defaultExportLayoutForFields($aiFields),
        ]);

        return response()->json(['status' => 'success', 'template' => $template]);
    }

    public function editMerchant(SlipTemplate $merchant) {
        if ($merchant->user_id !== auth()->id()) abort(403);
        $promptFields = $this->normalizedAiFieldDefinitions($merchant->ai_fields ?? []);
        $exportLayout = $this->resolvedExportLayoutForTemplate($merchant);
        $stores = Merchant::where('user_id', auth()->id())->orderBy('name')->get();
        return view('admin.template-edit', compact('merchant', 'promptFields', 'exportLayout', 'stores'));
    }

    public function updateMerchantMapping(Request $request, SlipTemplate $merchant) {
        if ($merchant->user_id !== auth()->id()) abort(403);
        
        $aiFields = is_string($request->ai_fields) ? json_decode($request->ai_fields, true) : $request->ai_fields;
        $normalized = $this->normalizedAiFieldDefinitions($aiFields ?: []);

        $merchant->update([
            'name' => $request->name,
            'merchant_id' => $request->merchant_id,
            'main_instruction' => $request->main_instruction,
            'ai_fields' => $normalized,
            'export_layout' => $normalized, // Simple sync for now
        ]);

        return response()->json(['status' => 'success']);
    }

    public function deleteMerchant(SlipTemplate $merchant) {
        if ($merchant->user_id !== auth()->id()) abort(403);
        $merchant->delete();
        return response()->json(['status' => 'success']);
    }

    // --- Slip Registry ---
    public function slipReader(Request $request) {
        $templates = SlipTemplate::with('merchant')->where('user_id', auth()->id())->get();
        $slips = Slip::with('template.merchant')
            ->where('user_id', auth()->id())
            ->latest('processed_at')
            ->paginate(50);

        $slips->getCollection()->transform(function($slip) {
            $data = $slip->extracted_data ?: [];
            $slip->display_shop = $data['shop_name'] ?? $data['store_name'] ?? $slip->template->merchant->name ?? 'Unknown';
            $slip->display_date = $data['date'] ?? $data['transaction_date'] ?? $slip->processed_at->format('d/m/Y');
            $slip->display_amount = $data['total_amount'] ?? $data['total'] ?? $data['final_total'] ?? 0;
            return $slip;
        });

        return view('admin.slip', compact('templates', 'slips'));
    }

    public function editSlip(Slip $slip) {
        if ($slip->user_id !== auth()->id()) abort(403);
        $exportColumns = $slip->template->ai_fields ?? [];
        return view('admin.slip-edit', compact('slip', 'exportColumns'));
    }

    public function processSlip(Request $request) {
        $request->validate(['image' => 'required|image', 'template_id' => 'required|exists:slip_templates,id']);
        
        $template = SlipTemplate::findOrFail($request->template_id);
        $path = $request->file('image')->store('slips', 'public');
        
        try {
            $data = $this->geminiService->extractDataFromImage(Storage::disk('public')->path($path), [
                'ai_fields' => $template->ai_fields,
                'main_instruction' => $template->main_instruction
            ]);

            Slip::create([
                'user_id' => auth()->id(),
                'slip_template_id' => $template->id,
                'image_path' => $path,
                'extracted_data' => $data,
                'processed_at' => now(),
            ]);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateSlip(Request $request, Slip $slip) {
        if ($slip->user_id !== auth()->id()) abort(403);
        $data = is_string($request->data) ? json_decode($request->data, true) : $request->data;
        $slip->update(['extracted_data' => $data]);
        return response()->json(['status' => 'success']);
    }

    public function deleteSlip(Slip $slip) {
        if ($slip->user_id !== auth()->id()) abort(403);
        Storage::disk('public')->delete($slip->image_path);
        $slip->delete();
        return response()->json(['status' => 'success']);
    }

    public function exportExcel(Request $request) {
        $templates = SlipTemplate::where('user_id', auth()->id())->get();
        if ($templates->isEmpty()) {
            return redirect()->route('admin.slip.index')->with('error', 'No extraction profiles found to build export schemas.');
        }

        $sheets = [];
        foreach ($templates as $template) {
            $slips = Slip::where('slip_template_id', $template->id)->latest('processed_at')->get();
            if ($slips->isEmpty()) continue;

            $exportFields = $template->export_layout ?: $template->ai_fields ?: [];
            if (empty($exportFields)) continue;

            // Phase 1: Scan for Array Fields mapping
            $baseHeadings = ['Processed Date'];
            $baseKeys = [];
            $arrayKeys = []; // Auto-detect array of objects (like line items)

            foreach ($exportFields as $field) {
                $isArray = false;
                $subKeys = [];
                // Peek into slips to detect if this field returns array of items consistently
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

            // Phase 2: Construct Final Header Columns
            $finalHeadings = $baseHeadings;
            foreach ($arrayKeys as $parentKey => $sKeys) {
                $parentLabel = collect($exportFields)->firstWhere('key', $parentKey)['label'] ?? ucfirst($parentKey);
                foreach ($sKeys as $sk) {
                    $finalHeadings[] = $parentLabel . ' - ' . ucfirst($sk);
                }
            }

            // Phase 3: Flatten Rows
            $rows = [];
            foreach ($slips as $slip) {
                $data = $slip->extracted_data ?? [];
                
                // Build base template row
                $baseRow = [$slip->processed_at->format('Y-m-d H:i:s')];
                foreach ($baseKeys as $key) {
                    $val = $data[$key] ?? '';
                    $baseRow[] = is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : $val;
                }

                if (empty($arrayKeys)) {
                    $rows[] = $baseRow;
                    continue;
                }

                // If the slip has item arrays, expand into multiple rows (Parallel Fill)
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
                            // Safely extract sub values, fallback to string if deeply nested
                            $subVal = $item[$sk] ?? '';
                            $row[] = is_array($subVal) ? json_encode($subVal, JSON_UNESCAPED_UNICODE) : $subVal;
                        }
                    }
                    $rows[] = $row;
                }
            }

            $sheets[] = [
                'title' => substr(preg_replace('/[^a-zA-Z0-9\s]/', '', $template->name), 0, 31) ?: 'Export',
                'headings' => $finalHeadings,
                'rows' => $rows,
            ];
        }

        if (empty($sheets)) {
            return redirect()->route('admin.slip.index')->with('error', 'No processed slip data available to export.');
        }

        return Excel::download(new SlipWorkbookExport($sheets), 'slips_export_' . date('Y_m_d_His') . '.xlsx');
    }

    public function suggestPrompt(Request $request) {
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

    // --- Helpers ---
    private function normalizedAiFieldDefinitions(array $aiFields): array {
        $definitions = [];
        foreach ($aiFields as $key => $value) {
            $fieldKey = is_string($key) ? $key : (is_array($value) ? ($value['key'] ?? '') : $value);
            $fieldLabel = is_array($value) ? ($value['label'] ?? $fieldKey) : (is_string($key) ? $value : $fieldKey);
            
            if (!$fieldKey) continue;
            
            $definitions[] = [
                'key' => strtolower(preg_replace('/[^a-z0-9]/i', '_', $fieldKey)),
                'label' => $fieldLabel,
                'type' => 'text'
            ];
        }
        return $definitions;
    }

    private function resolvedExportLayoutForTemplate($template) {
        return $template->ai_fields ?? [];
    }

    private function defaultExportLayoutForFields($fields) {
        return $fields;
    }

    public function users() {
        $users = User::latest()->get();
        return view('admin.users', compact('users'));
    }
}

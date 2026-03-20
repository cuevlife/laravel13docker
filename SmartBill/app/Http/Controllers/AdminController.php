<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Slip;
use App\Models\User;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function dashboard()
    {
        $stats = [
            'users_count' => User::count(),
            'merchants_count' => Merchant::count(),
            'slips_count' => Slip::count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }

    public function slipReader()
    {
        $merchants = Merchant::all();
        $slips = Slip::with(['merchant', 'user'])->latest()->paginate(10);
        return view('admin.slip-reader', compact('merchants', 'slips'));
    }

    public function processSlip(Request $request)
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'image' => 'required|image|max:2048',
        ]);

        try {
            // Save file
            $path = $request->file('image')->store('slips', 'public');
            $fullPath = storage_path('app/public/' . $path);

            // 1. AI EXTRACTION
            $extractedData = $this->geminiService->extractDataFromImage($fullPath);
            
            if (isset($extractedData['status']) && $extractedData['status'] === 'error') {
                return response()->json(['status' => 'error', 'message' => $extractedData['message']], 500);
            }

            // 2. MAPPING LOGIC (Inspired by Concept)
            $merchant = Merchant::find($request->merchant_id);
            $config = $merchant->config ?: [];
            
            // Map item names to item codes if config exists
            if (!empty($extractedData['items']) && !empty($config['item_code_mapping'])) {
                foreach ($extractedData['items'] as &$item) {
                    $item['code'] = $config['item_code_mapping'][$item['name']] ?? 'N/A';
                }
            }

            // Add vendor code from merchant config
            $extractedData['shop_code'] = $config['vendor_code'] ?? 'V-SET';

            // 3. SAVE TO DB
            $slip = Slip::create([
                'user_id' => auth()->id(),
                'merchant_id' => $request->merchant_id,
                'image_path' => $path,
                'extracted_data' => $extractedData,
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            // AJAX response for better UX (High-Tech style)
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'success', 'data' => $slip]);
            }

            return back()->with('success', 'Slip processed successfully!');

        } catch (\Exception $e) {
            Log::error("Process Slip Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateSlip(Request $request, Slip $slip)
    {
        $request->validate(['data' => 'required|json']);
        
        $slip->update([
            'extracted_data' => json_decode($request->data, true),
        ]);

        return response()->json(['status' => 'success']);
    }

    public function deleteSlip(Slip $slip)
    {
        // Delete image file
        Storage::disk('public')->delete($slip->image_path);
        $slip->delete();
        
        return response()->json(['status' => 'success']);
    }

    // --- Merchant & Mapping Management ---

    public function merchants()
    {
        $merchants = Merchant::latest()->get();
        return view('admin.merchants', compact('merchants'));
    }

    public function storeMerchant(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        Merchant::create([
            'name' => $request->name,
            'config' => [
                'vendor_code' => 'V-' . strtoupper(str()->random(5)),
                'item_code_mapping' => []
            ]
        ]);

        return back()->with('success', 'Merchant created!');
    }

    public function updateMerchantMapping(Request $request, Merchant $merchant)
    {
        $request->validate([
            'vendor_code' => 'nullable|string',
            'item_code_mapping' => 'nullable|json'
        ]);

        $config = $merchant->config ?: [];
        $config['vendor_code'] = $request->vendor_code;
        $config['item_code_mapping'] = json_decode($request->item_code_mapping, true) ?: [];

        $merchant->update(['config' => $config]);

        return response()->json(['status' => 'success']);
    }

    public function users()
    {
        $users = User::latest()->get();
        return view('admin.users', compact('users'));
    }
}

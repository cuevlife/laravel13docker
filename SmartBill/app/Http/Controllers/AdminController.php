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
            $path = $request->file('image')->store('slips', 'public');
            $fullPath = storage_path('app/public/' . $path);

            $extractedData = $this->geminiService->extractDataFromImage($fullPath);
            
            if (isset($extractedData['status']) && $extractedData['status'] === 'error') {
                return response()->json(['status' => 'error', 'message' => $extractedData['message']], 500);
            }

            $merchant = Merchant::find($request->merchant_id);
            $config = $merchant->config ?: [];
            
            // Apply Neural Mapping
            if (!empty($extractedData['items'])) {
                foreach ($extractedData['items'] as &$item) {
                    $item['code'] = $config['item_code_mapping'][$item['name']] ?? 'N/A';
                }
            }
            $extractedData['shop_code'] = $config['vendor_code'] ?? 'NODE_UNSET';

            $slip = Slip::create([
                'user_id' => auth()->id(),
                'merchant_id' => $request->merchant_id,
                'image_path' => $path,
                'extracted_data' => $extractedData,
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            return response()->json(['status' => 'success', 'data' => $slip]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateSlip(Request $request, Slip $slip)
    {
        $request->validate(['data' => 'required|json']);
        $slip->update(['extracted_data' => json_decode($request->data, true)]);
        return response()->json(['status' => 'success']);
    }

    public function deleteSlip(Slip $slip)
    {
        Storage::disk('public')->delete($slip->image_path);
        $slip->delete();
        return response()->json(['status' => 'success']);
    }

    public function exportExcel()
    {
        $slips = Slip::with('merchant')->latest()->get();
        
        $headers = [
            'DocDate', 'VendorCode', 'VendorName', 'CustomerName', 
            'PetName', 'ItemCode', 'ItemName', 'Amount', 'NetTotal'
        ];

        return response()->streamDownload(function() use ($slips, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            foreach ($slips as $slip) {
                $data = $slip->extracted_data;
                $items = $data['items'] ?? [[]]; // At least one row per slip

                foreach ($items as $item) {
                    fputcsv($file, [
                        $data['date'] ?? $slip->processed_at->format('Y-m-d'),
                        $data['shop_code'] ?? 'N/A',
                        $data['shop_name'] ?? $slip->merchant->name,
                        $data['customer_name'] ?? 'N/A',
                        $data['pet_info'] ?? 'N/A',
                        $item['code'] ?? 'N/A',
                        $item['name'] ?? 'N/A',
                        $item['price'] ?? 0,
                        $data['final_total'] ?? 0,
                    ]);
                }
            }
            fclose($file);
        }, 'Neural_Export_' . now()->format('Ymd_His') . '.csv');
    }

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
            'config' => ['vendor_code' => 'V-' . strtoupper(str()->random(4)), 'item_code_mapping' => []]
        ]);
        return back()->with('success', 'Node Linked!');
    }

    public function updateMerchantMapping(Request $request, Merchant $merchant)
    {
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

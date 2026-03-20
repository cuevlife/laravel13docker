<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Slip;
use App\Models\User;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    protected EncryptionService $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
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

        $path = $request->file('image')->store('slips', 'public');

        // Mock AI Extraction logic
        // In reality, you'd send this to an OCR/AI service
        $extractedData = [
            'transaction_id' => 'TXN-' . strtoupper(str()->random(8)),
            'amount' => rand(100, 5000) . '.00',
            'date' => now()->format('Y-m-d H:i:s'),
            'sender' => 'User ' . auth()->id(),
            'receiver' => Merchant::find($request->merchant_id)->name,
        ];

        $slip = Slip::create([
            'user_id' => auth()->id(),
            'merchant_id' => $request->merchant_id,
            'image_path' => $path,
            'extracted_data' => $extractedData,
            'status' => 'completed',
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Slip processed successfully!');
    }

    public function exportExcel()
    {
        // For demonstration, we'll create a simple export
        // In a real app, you'd use a dedicated Export class
        $slips = Slip::with('merchant')->get();
        
        $data = $slips->map(function($slip) {
            return array_merge([
                'Merchant' => $slip->merchant->name,
                'Status' => $slip->status,
                'Processed At' => $slip->processed_at,
            ], $slip->extracted_data ?? []);
        });

        // This is a simplified export logic
        return response()->streamDownload(function() use ($data) {
            $file = fopen('php://output', 'w');
            if ($data->count() > 0) {
                fputcsv($file, array_keys($data->first()));
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
            }
            fclose($file);
        }, 'slips_export_' . now()->format('YmdHis') . '.csv');
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
            'config' => ['fields' => ['amount', 'date', 'transaction_id']]
        ]);

        return back()->with('success', 'Merchant created!');
    }

    public function users()
    {
        $users = User::latest()->get();
        return view('admin.users', compact('users'));
    }
}

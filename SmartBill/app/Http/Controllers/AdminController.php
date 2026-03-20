<?php

namespace App\Http\Controllers;

use App\Models\SlipTemplate;
use App\Models\Slip;
use App\Models\User;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService) {
        $this->geminiService = $geminiService;
    }

    public function dashboard() {
        $stats = [
            'users_count' => User::count(),
            'templates_count' => SlipTemplate::count(),
            'slips_count' => Slip::count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }

    public function slipReader() {
        $templates = SlipTemplate::where('user_id', auth()->id())->get();
        $slips = Slip::with('template')->where('user_id', auth()->id())->latest()->paginate(12);
        return view('admin.slip-reader', compact('templates', 'slips'));
    }

    public function processSlip(Request $request) {
        $request->validate([
            'template_id' => 'required|exists:slip_templates,id',
            'image' => 'required|image|max:4096',
        ]);

        try {
            $template = SlipTemplate::find($request->template_id);
            $path = $request->file('image')->store('slips', 'public');
            $fullPath = storage_path('app/public/' . $path);

            // Pass template config to AI
            $config = [
                'main_instruction' => $template->main_instruction,
                'ai_fields' => $template->ai_fields
            ];

            $extractedData = $this->geminiService->extractDataFromImage($fullPath, $config);
            
            if (isset($extractedData['status']) && $extractedData['status'] === 'error') {
                return response()->json(['status' => 'error', 'message' => $extractedData['message']], 500);
            }

            $slip = Slip::create([
                'user_id' => auth()->id(),
                'slip_template_id' => $template->id,
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

    public function exportExcel(Request $request) {
        $query = Slip::with('template')->where('user_id', auth()->id());
        if($request->has('template_id')) $query->where('slip_template_id', $request->template_id);
        $slips = $query->latest()->get();

        return response()->streamDownload(function() use ($slips) {
            $file = fopen('php://output', 'w');
            // Headers will be dynamic based on first slip's keys
            if($slips->count() > 0) {
                $keys = array_keys($slips->first()->extracted_data);
                array_unshift($keys, 'Template', 'Date_Processed');
                fputcsv($file, $keys);

                foreach ($slips as $slip) {
                    $row = array_values($slip->extracted_data);
                    array_unshift($row, $slip->template->name, $slip->processed_at);
                    fputcsv($file, $row);
                }
            }
            fclose($file);
        }, 'SmartBill_Export_' . now()->format('Ymd_His') . '.csv');
    }

    // --- Templates CRUD (Replacing Merchants) ---
    public function merchants() {
        $templates = SlipTemplate::where('user_id', auth()->id())->latest()->get();
        return view('admin.merchants', compact('templates'));
    }

    public function storeMerchant(Request $request) {
        $request->validate(['name' => 'required|string|max:255']);
        SlipTemplate::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'main_instruction' => 'Extract key information from this document.',
            'ai_fields' => ['date' => true, 'total' => true, 'items' => true]
        ]);
        return back();
    }

    public function updateMerchantMapping(Request $request, SlipTemplate $merchant) {
        $request->validate([
            'main_instruction' => 'required|string',
            'ai_fields' => 'required|json'
        ]);

        $merchant->update([
            'main_instruction' => $request->main_instruction,
            'ai_fields' => json_decode($request->ai_fields, true)
        ]);

        return response()->json(['status' => 'success']);
    }

    public function deleteSlip(Slip $slip) {
        Storage::disk('public')->delete($slip->image_path);
        $slip->delete();
        return response()->json(['status' => 'success']);
    }

    public function updateSlip(Request $request, Slip $slip) {
        $slip->update(['extracted_data' => json_decode($request->data, true)]);
        return response()->json(['status' => 'success']);
    }

    public function users() {
        $users = User::latest()->get();
        return view('admin.users', compact('users'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Slip;
use App\Models\SlipTemplate;
use App\Models\Merchant;
use App\Services\GeminiService;
use App\Support\IntelligencePresets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SlipController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|image|max:10240',
        ]);

        $paths = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                \App\Support\ImageOptimizer::optimizeUpload($file, 1600, 1600, 85);
                $filename = time() . '-' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('temp-uploads', $filename, 'public');
                $paths[] = [
                    'name' => $file->getClientOriginalName(),
                    'temp_path' => $path,
                    'url' => asset('storage/' . $path)
                ];
            }
        }

        return response()->json(['status' => 'success', 'files' => $paths]);
    }

    public function process(Request $request)
    {
        $data = $request->validate([
            'temp_path' => 'required|string',
            'template_id' => 'required|string',
        ]);

        $tenant = Merchant::findOrFail(session('active_project_id'));
        $user = Auth::user();
        $fullPath = Storage::disk('public')->path($data['temp_path']);

        try {
            $geminiService = app(GeminiService::class);
            $template = null;
            $dynamicFields = null;
            $dynamicInstruction = null;

            if ($data['template_id'] === 'auto') {
                $presetKeys = array_keys(IntelligencePresets::all());
                $detectedType = $geminiService->identifyStoreFromImage($fullPath, $presetKeys) ?: 'retail';
                $preset = IntelligencePresets::all()[$detectedType];
                $dynamicFields = $preset['ai_fields'];
                $dynamicInstruction = $preset['main_instruction'];
                
                $template = SlipTemplate::firstOrCreate(
                    ['merchant_id' => $tenant->id, 'name' => \"Auto: {\['name']}\"],
                    ['user_id' => $user->id, 'main_instruction' => $dynamicInstruction, 'ai_fields' => $dynamicFields]
                );
            } else {
                $template = SlipTemplate::where('merchant_id', $tenant->id)->findOrFail($data['template_id']);
                $dynamicFields = $template->ai_fields;
                $dynamicInstruction = $template->main_instruction;
            }

            $extractedData = $geminiService->extractDataFromImage($fullPath, [
                'ai_fields' => $dynamicFields,
                'main_instruction' => $dynamicInstruction ?? '',
            ]);

            // Move from temp to final
            $finalPath = 'slips/' . basename($data['temp_path']);
            if (!Storage::disk('public')->exists('slips')) {
                Storage::disk('public')->makeDirectory('slips');
            }
            Storage::disk('public')->move($data['temp_path'], $finalPath);

            $slip = Slip::create([
                'merchant_id' => $tenant->id,
                'user_id' => $user->id,
                'slip_template_id' => $template->id,
                'uid' => 'SLP-' . strtoupper(Str::random(8)),
                'image_path' => $finalPath,
                'extracted_data' => $extractedData,
                'processed_at' => now(),
                'workflow_status' => Slip::WORKFLOW_PENDING,
                'total_amount' => $extractedData['total_amount'] ?? 0,
            ]);

            return response()->json(['status' => 'success', 'slip' => $slip]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/";

    public function __construct()
    {
        // ในระบบจริงควรใช้ config หรือ env แต่ตอนนี้ดึงจาก concept มาก่อนตามที่คุณต้องการ
        $this->apiKey = 'AIzaSyC7iF_-tQPu-RVIrfCFdW5TgUcUW8HfwxQ';
        $this->model = 'gemini-2.0-flash'; // ใช้ flash 2.0 เพื่อความเร็ว
    }

    public function extractDataFromImage(string $imagePath): array
    {
        if (!file_exists($imagePath)) {
            throw new \Exception("Image file not found at: " . $imagePath);
        }

        $base64Image = base64_encode(file_get_contents($imagePath));
        $prompt = $this->getDynamicPrompt();

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . $this->model . ":generateContent?key=" . $this->apiKey, [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $prompt],
                            [
                                "inline_data" => [
                                    "mime_type" => "image/jpeg",
                                    "data" => $base64Image
                                ]
                            ]
                        ]
                    ]
                ],
                "generationConfig" => [
                    "response_mime_type" => "application/json"
                ]
            ]);

            if ($response->failed()) {
                Log::error("Gemini API Error: " . $response->body());
                throw new \Exception("Gemini API failed to process the image.");
            }

            $data = $response->json();
            $responseText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            
            // Clean up JSON if AI adds markdown code blocks
            $cleanJson = preg_replace('/^```json\s*|```$/', '', trim($responseText));
            
            return json_decode($cleanJson, true) ?: [];

        } catch (\Exception $e) {
            Log::error("Gemini Extraction Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    protected function getDynamicPrompt(): string
    {
        return "Extract data from this receipt into RAW JSON.
        STRICT RULES:
        - Numbers ONLY for prices.
        - Date format: YYYY-MM-DD.
        - Items: Provide an array of objects with 'name' and 'price'.
        - Categories for items: Medicine, Service, Food, Lab/X-ray, Other.
        
        Return JSON with these keys: 
        date, shop_name, customer_name, pet_info, items, subtotal, deposit_deduction, final_total";
    }
}

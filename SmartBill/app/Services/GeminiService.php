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
        $this->apiKey = 'AIzaSyC7iF_-tQPu-RVIrfCFdW5TgUcUW8HfwxQ';
        $this->model = 'gemini-2.0-flash';
    }

    public function extractDataFromImage(string $imagePath, array $config): array
    {
        $base64Image = base64_encode(file_get_contents($imagePath));
        $prompt = $this->getDynamicPrompt($config);

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post($this->baseUrl . $this->model . ":generateContent?key=" . $this->apiKey, [
                    "contents" => [
                        [
                            "parts" => [
                                ["text" => $prompt],
                                ["inline_data" => ["mime_type" => "image/jpeg", "data" => $base64Image]]
                            ]
                        ]
                    ],
                    "generationConfig" => ["response_mime_type" => "application/json"]
                ]);

            if ($response->failed()) throw new \Exception("AI Engine Error");

            $data = $response->json();
            $responseText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            return json_decode(preg_replace('/^```json\s*|```$/', '', trim($responseText)), true) ?: [];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    protected function getDynamicPrompt(array $config): string
    {
        // === ใช้ Logic ที่คุณให้มาเป๊ะๆ ===
        $prompt = $config['main_instruction'] . "\n\nSTRICT RULES:\n";
        $prompt .= "- Numbers ONLY for prices.\n";
        $prompt .= "- Date format: YYYY-MM-DD.\n";
        $prompt .= "- Items: Provide 'name' (ข้อความในสลิป) and 'price' (ราคา).\n";
        $prompt .= "- Return RAW JSON with keys: ";
        
        $enabledKeys = [];
        foreach($config['ai_fields'] as $k => $enabled) {
            if($enabled) $enabledKeys[] = $k;
        }
        $prompt .= implode(", ", $enabledKeys) . "\n";
        
        return $prompt;
    }
}

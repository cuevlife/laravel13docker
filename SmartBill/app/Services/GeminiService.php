<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class GeminiService
{
    protected ?string $apiKey = null;
    protected string $model;
    protected string $baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/";

    public function __construct()
    {
        $this->model = config('services.gemini.model', 'gemini-1.5-flash');
        $this->resolveApiKey();
    }

    /**
     * Dynamically resolve the API key from the global pool (rotation)
     */
    protected function resolveApiKey(): void
    {
        $superAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();
        $settings = $superAdmin?->settings ?: [];
        $keys = $settings['gemini_api_keys'] ?? [];

        if (empty($keys)) {
            $this->apiKey = config('services.gemini.key');
            return;
        }

        // Simple rotation based on minute of the hour
        $index = (int) date('i') % count($keys);
        $this->apiKey = $keys[$index];

        // Increment usage count for this key
        $cacheKey = 'gemini_key_usage_' . md5($this->apiKey);
        \Illuminate\Support\Facades\Cache::increment($cacheKey);
    }

    public function setModel(string $model): self
    {
        $this->model = preg_replace('/^models\//', '', $model);
        return $this;
    }

    public function identifyStoreFromImage(string $imagePath, array $templateNames): ?string
    {
        if (!$this->apiKey || empty($templateNames)) return null;

        $base64Image = base64_encode(file_get_contents($imagePath));
        $storeList = implode(', ', array_map(fn($t) => '"' . $t . '"', $templateNames));
        
        $prompt = "Look at this receipt/invoice. Which of the following stores or profiles does it belong to?\n" .
                  "Choices: [" . $storeList . "]\n\n" .
                  "If you are confident it belongs to one of the choices, reply ONLY with the EXACT name from the choices. Do not add any punctuation or extra words.\n" .
                  "If it does not match ANY of the choices, reply ONLY with the word 'UNKNOWN'.";

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
                    "generationConfig" => ["temperature" => 0.0]
                ]);

            if ($response->status() === 429) {
                throw new \RuntimeException("AI API limit reached. Please wait a moment.");
            }

            if ($response->failed()) return null;

            $data = $response->json();
            $responseText = trim($data['candidates'][0]['content']['parts'][0]['text'] ?? 'UNKNOWN');
            if ($responseText === 'UNKNOWN' || $responseText === '') return null;
            return trim($responseText, '"\' ');
        } catch (\RuntimeException $re) {
            throw $re;
        } catch (\Exception $e) {
            Log::error("Gemini Auto-Detect Error: " . $e->getMessage());
            return null;
        }
    }

    public function suggestSchemaFromImage(string $imagePath): array
    {
        if (!$this->apiKey) {
            throw new \RuntimeException('Gemini API Key is not configured.');
        }

        $base64Image = base64_encode(file_get_contents($imagePath));
        $prompt = "Analyze this receipt/slip image and identify all important data fields that should be extracted. 

        Return a JSON object where:
        - Keys are the snake_case technical names (in English).
        - Values are the beautiful, friendly Display Labels in THAI (e.g., 'ที่อยู่', 'เบอร์โทร', 'เลขผู้เสียภาษี').

        Example Output:
        {
            \"shop_name\": \"ชื่อร้าน\",
            \"tax_id\": \"เลขประจำตัวผู้เสียภาษี\",
            \"total_amount\": \"ยอดรวมทั้งสิ้น\",
            \"items\": \"รายการสินค้า\"
        }";

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
                    "generationConfig" => [
                        "response_mime_type" => "application/json",
                        "temperature" => 0.1
                    ]
                ]);

            if ($response->status() === 429) {
                throw new \RuntimeException("AI API limit reached. Please wait a moment.");
            }

            if ($response->failed()) {
                $errorBody = $response->json();
                throw new \RuntimeException($errorBody['error']['message'] ?? 'AI Engine Error');
            }

            $data = $response->json();
            $responseText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            return json_decode(preg_replace('/^```json\s*|```$/', '', trim($responseText)), true) ?: [];
        } catch (\RuntimeException $re) {
            throw $re;
        } catch (\Exception $e) {
            Log::error("Gemini Suggest Schema Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function extractDataFromImage(string $imagePath, array $config): array
    {
        if (!$this->apiKey) {
            return ['status' => 'error', 'message' => 'Gemini API Key is missing.'];
        }

        $base64Image = base64_encode(file_get_contents($imagePath));
        $fieldDefinitions = $this->normalizedFieldDefinitions($config);
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
                    "generationConfig" => [
                        "response_mime_type" => "application/json",
                        "temperature" => 0.0
                    ]
                ]);

            if ($response->status() === 429) {
                throw new \RuntimeException("AI API limit reached. Please wait a moment.");
            }

            if ($response->failed()) {
                $errorBody = $response->json();
                throw new \Exception($errorBody['error']['message'] ?? 'AI Engine Error');
            }

            $data = $response->json();
            $responseText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            $decoded = json_decode(preg_replace('/^```json\s*|```$/', '', trim($responseText)), true);
            $payload = is_array($decoded) ? $decoded : [];

            return $this->normalizeExtractedPayload($payload, $fieldDefinitions);
        } catch (\RuntimeException $re) {
            throw $re;
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    protected function getDynamicPrompt(array $config): string
    {
        $requestedFields = $this->normalizedFieldDefinitions($config);
        $fieldGuide = "";

        foreach ($requestedFields as $field) {
            $key = $field['key'] ?? 'unknown';
            $label = $field['label'] ?? $key;
            $type = $field['type'] ?? 'text';
            $hint = !empty($field['hint']) ? " (Special Instruction: {$field['hint']})" : "";
            $fieldGuide .= "- `{$key}`: This is the '{$label}'. Expected type: {$type}.{$hint}\n";
        }

        return "### ROLE: PROFESSIONAL DATA EXTRACTION ENGINE\n" .
               "### TASK: EXTRACT SPECIFIC FIELDS FROM THE ATTACHED IMAGE\n\n" .
               "### FIELDS TO EXTRACT:\n" . $fieldGuide . 
               "\n### RULES:\n1. RETURN ONLY RAW VALID JSON.\n2. CLEAN DATA: No prefixes.\n3. Numbers: pure number.\n4. Dates: YYYY-MM-DD.\n5. Thai: Maintain encoding.\n\n### FINAL OUTPUT FORM: VALID JSON OBJECT ONLY.";
    }

    protected function normalizedFieldDefinitions(array $config): array
    {
        $fields = $this->aiFieldDefinitions($config['ai_fields'] ?? []);
        if ($fields !== []) return $fields;

        return [
            ['key' => 'shop_name', 'type' => 'text'],
            ['key' => 'date', 'type' => 'date'],
            ['key' => 'final_total', 'type' => 'number'],
            ['key' => 'items', 'type' => 'array'],
        ];
    }

    protected function aiFieldDefinitions(array $aiFields): array
    {
        $definitions = [];
        foreach ($aiFields as $key => $value) {
            if (is_array($value)) {
                $definitions[] = [
                    'key' => trim((string) ($value['key'] ?? '')),
                    'label' => trim((string) ($value['label'] ?? '')) ?: null,
                    'type' => trim((string) ($value['type'] ?? '')) ?: null,
                    'hint' => trim((string) ($value['hint'] ?? '')) ?: null,
                ];
            }
        }
        return $definitions;
    }

    protected function normalizeExtractedPayload(array $payload, array $fieldDefinitions): array
    {
        $normalized = [];
        foreach ($fieldDefinitions as $field) {
            $fieldKey = $field['key'];
            $value = $payload[$fieldKey] ?? null;
            if (($field['type'] ?? null) === 'array' && $value !== null && !is_array($value)) {
                $value = [$value];
            }
            $normalized[$fieldKey] = $value;
        }
        $normalized['__metadata'] = ['confidence_score' => 1.0, 'is_reliable' => true];
        return $normalized;
    }
}

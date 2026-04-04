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
        $this->apiKey = config('services.gemini.key');
        $this->model = config('services.gemini.model', 'gemini-1.5-flash');
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
                ->post($this->baseUrl . "gemini-1.5-flash:generateContent?key=" . $this->apiKey, [
                    "contents" => [
                        [
                            "parts" => [
                                ["text" => $prompt],
                                ["inline_data" => ["mime_type" => "image/jpeg", "data" => $base64Image]]
                            ]
                        ]
                    ],
                    "generationConfig" => ["temperature" => 0.0] // Very strict, minimal hallucination
                ]);

            if ($response->failed()) return null;

            $data = $response->json();
            $responseText = trim($data['candidates'][0]['content']['parts'][0]['text'] ?? 'UNKNOWN');
            
            if ($responseText === 'UNKNOWN' || $responseText === '') return null;
            
            // Post-process to aggressively strip quotes
            $responseText = trim($responseText, '"\' ');
            
            return $responseText;
        } catch (\Exception $e) {
            Log::error("Gemini Auto-Detect Error: " . $e->getMessage());
            return null;
        }
    }

    public function suggestSchemaFromImage(string $imagePath): array
    {
        if (!$this->apiKey) {
            throw new \RuntimeException('Gemini API Key is not configured in .env');
        }

        $base64Image = base64_encode(file_get_contents($imagePath));
        $prompt = "Analyze this receipt/slip image and identify all important data fields that should be extracted (e.g., date, total, tax, items, shop name, branch, etc.). 
        
        Return a JSON object where:
        - Keys are the snake_case technical names for the fields.
        - Values are the descriptive labels for these fields in Thai or English as seen on the slip.
        
        Example Output:
        {
            \"date\": \"วันที่\",
            \"shop_name\": \"ชื่อร้าน\",
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

            if ($response->failed()) {
                $errorBody = $response->json();
                $errorMessage = $errorBody['error']['message'] ?? 'AI Engine Error';
                Log::error("Gemini Suggest Schema Failure: " . $errorMessage, ['response' => $errorBody]);
                throw new \RuntimeException($errorMessage);
            }

            $data = $response->json();
            $responseText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            $schema = json_decode(preg_replace('/^```json\s*|```$/', '', trim($responseText)), true);

            if (!is_array($schema) || $schema === []) {
                Log::error('Gemini Suggest Schema Error: Invalid schema response', ['response' => $data]);
                throw new \RuntimeException('AI returned an empty or invalid field suggestion.');
            }

            return $schema;
        } catch (\Exception $e) {
            Log::error("Gemini Suggest Schema Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function extractDataFromImage(string $imagePath, array $config): array
    {
        if (!$this->apiKey) {
            return ['status' => 'error', 'message' => 'Gemini API Key is not configured in .env'];
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
                        "temperature" => 0.0,
                        "topP" => 0.1,
                        "topK" => 1
                    ]
                ]);

            if ($response->failed()) {
                $errorBody = $response->json();
                $errorMessage = $errorBody['error']['message'] ?? 'AI Engine Error';
                Log::error("Gemini API Failure: " . $errorMessage, ['response' => $errorBody]);
                throw new \Exception($errorMessage);
            }

            $data = $response->json();
            $responseText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            $decoded = json_decode(preg_replace('/^```json\s*|```$/', '', trim($responseText)), true);
            $payload = is_array($decoded) ? $decoded : [];

            return $this->normalizeExtractedPayload($payload, $fieldDefinitions);

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    protected function getDynamicPrompt(array $config): string
    {
        $requestedFields = $this->normalizedFieldDefinitions($config);
        $headerGuide = "";
        foreach ($requestedFields as $field) {
            $headerGuide .= "- {$field['key']}: {$this->fieldInstruction($field)}\n";
        }

        $prompt = "### ROLE: PRO-DATA ARCHIVAL EXTRACTOR\n" .
                  "### GOAL: EXTRACT DATA FROM THIS RECEIPT IMAGE WITH 100% PRECISION\n\n" .
                  "### HEADERS TO EXTRACT:\n" . 
                  $headerGuide . 
                  "\n### STRICT RULES:\n" .
                  "1. Return ONLY raw JSON.\n" .
                  "2. Use the exact technical keys provided above.\n" .
                  "3. Every key must exist in the JSON output. If the data is missing or you are less than 90% sure, use null.\n" .
                  "4. For numbers: Return as float/int. Remove commas and currency symbols (e.g. '1,234.50 THB' -> 1234.5).\n" .
                  "5. For dates: Use YYYY-MM-DD format only. If year is missing, assume current year.\n" .
                  "6. For text: Maintain the original case unless specified otherwise.\n" .
                  "7. " . ($config['main_instruction'] ?? "Extract all data accurately.");

        return $prompt;
    }

    protected function normalizedFieldDefinitions(array $config): array
    {
        $fields = $this->aiFieldDefinitions($config['ai_fields'] ?? []);
        if ($fields !== []) {
            return $fields;
        }

        $fallbackFields = [];

        foreach ($config['export_layout'] ?? [] as $column) {
            $fieldKey = null;
            $fieldLabel = null;

            if (is_array($column) && !empty($column['key'])) {
                $fieldKey = trim((string) $column['key']);
                $fieldLabel = trim((string) ($column['label'] ?? '')) ?: null;
            } elseif (is_string($column) && $column !== '') {
                $fieldKey = trim($column);
            }

            if (!$fieldKey) {
                continue;
            }

            $fallbackFields[] = array_filter([
                'key' => $fieldKey,
                'label' => $fieldLabel,
                'type' => $this->guessFieldType($fieldKey),
            ], fn ($value) => $value !== null && $value !== '');
        }

        if ($fallbackFields !== []) {
            return $fallbackFields;
        }

        return [
            ['key' => 'shop_name', 'type' => 'text'],
            ['key' => 'shop_code', 'type' => 'text'],
            ['key' => 'date', 'type' => 'date'],
            ['key' => 'subtotal', 'type' => 'number'],
            ['key' => 'deposit_deduction', 'type' => 'number'],
            ['key' => 'final_total', 'type' => 'number'],
            ['key' => 'items', 'type' => 'array'],
        ];
    }

    protected function aiFieldDefinitions(array $aiFields): array
    {
        $definitions = [];

        foreach ($aiFields as $key => $value) {
            if (is_array($value) && !is_string($key)) {
                $fieldKey = trim((string) ($value['key'] ?? $value['name'] ?? ''));
                if ($fieldKey === '') {
                    continue;
                }

                $definitions[] = [
                    'key' => $fieldKey,
                    'label' => trim((string) ($value['label'] ?? '')) ?: null,
                    'type' => trim((string) ($value['type'] ?? '')) ?: null,
                    'hint' => trim((string) ($value['hint'] ?? $value['instruction'] ?? '')) ?: null,
                    'required' => array_key_exists('required', $value) ? (bool) $value['required'] : null,
                    'example' => trim((string) ($value['example'] ?? '')) ?: null,
                ];

                continue;
            }

            if (is_string($key) && $key !== '') {
                $definitions[] = ['key' => trim($key), 'type' => $this->guessFieldType(trim($key))];
                continue;
            }

            if (is_string($value) && $value !== '') {
                $definitions[] = ['key' => trim($value), 'type' => $this->guessFieldType(trim($value))];
            }
        }

        return $definitions;
    }

    protected function normalizeExtractedPayload(array $payload, array $fieldDefinitions): array
    {
        if ($fieldDefinitions === []) {
            return $payload;
        }

        $normalized = [];
        foreach ($fieldDefinitions as $field) {
            $fieldKey = trim((string) ($field['key'] ?? ''));
            if ($fieldKey === '') {
                continue;
            }

            $value = $payload[$fieldKey] ?? null;

            if (($field['type'] ?? null) === 'array' && $value !== null && !is_array($value)) {
                $value = [$value];
            }

            $normalized[$fieldKey] = $value;
        }

        $normalized['__metadata'] = [
            'confidence_score' => count(array_filter($normalized, fn($v) => !is_null($v))) / max(count($normalized), 1),
            'is_reliable' => $this->checkReliability($normalized, $fieldDefinitions),
        ];

        return $normalized;
    }

    protected function checkReliability(array $data, array $fields): bool
    {
        // Must have at least shop_name and total_amount for a receipt to be "reliable"
        $requiredKeys = ['shop_name', 'total_amount', 'date'];
        foreach ($requiredKeys as $key) {
            foreach ($fields as $f) {
                if ($f['key'] === $key && (empty($data[$key]) || $data[$key] == 0)) {
                    return false;
                }
            }
        }
        return true;
    }

    protected function guessFieldType(string $key): string
    {
        $normalized = strtolower(trim($key));

        return match (true) {
            $normalized === 'items' => 'array',
            str_contains($normalized, 'date') => 'date',
            str_contains($normalized, 'time') => 'datetime',
            str_contains($normalized, 'total'),
            str_contains($normalized, 'amount'),
            str_contains($normalized, 'price'),
            str_contains($normalized, 'subtotal'),
            str_contains($normalized, 'discount'),
            str_contains($normalized, 'deposit') => 'number',
            default => 'text',
        };
    }

    protected function fieldInstruction(array $field): string
    {
        $fieldKey = (string) ($field['key'] ?? '');
        $parts = [];

        $parts[] = match ($fieldKey) {
            'shop_name' => 'store or merchant name',
            'shop_code' => 'merchant code, branch code, or terminal code if visible',
            'date' => 'transaction date',
            'subtotal' => 'subtotal amount before discounts',
            'deposit_deduction' => 'discount, voucher, or deduction amount',
            'final_total', 'total' => 'final net amount paid',
            'items' => "array of line items with at least 'name' and 'price'",
            default => 'extract this field from the document if present',
        };

        if (!empty($field['label']) && strcasecmp((string) $field['label'], $fieldKey) !== 0) {
            $parts[] = "display label: '{$field['label']}'";
        }

        if (!empty($field['type'])) {
            $parts[] = "expected type: {$field['type']}";
        }

        if (!empty($field['hint'])) {
            $parts[] = "store hint: {$field['hint']}";
        }

        if (($field['required'] ?? false) === true) {
            $parts[] = 'treat as important and only return null when truly missing';
        }

        if (!empty($field['example'])) {
            $parts[] = "example value: {$field['example']}";
        }

        return implode('; ', array_filter($parts));
    }

}


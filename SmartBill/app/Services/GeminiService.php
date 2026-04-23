<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SystemConfig;

class GeminiService
{
    protected const RETRY_DELAYS_MS = [3000, 10000, 20000];

    protected ?string $apiKey = null;
    protected string $model;
    protected string $apiVersion = 'v1beta';
    protected array $config = [];

    public function __construct()
    {
        $this->loadConfig();
    }

    protected function loadConfig(): void
    {
        $configs = SystemConfig::whereIn('config_key', ['gemini_model', 'gemini_api_keys'])->pluck('config_value', 'config_key');

        $this->model = $configs['gemini_model'] ?? config('services.gemini.model', 'gemini-1.5-flash');
        
        $keysValue = $configs['gemini_api_keys'] ?? null;
        if ($keysValue) {
            $keys = json_decode($keysValue, true) ?: [];
            if (!empty($keys)) {
                // Randomly select one key from the pool
                $this->apiKey = $keys[array_rand($keys)];
            }
        }

        if (!$this->apiKey) {
            $this->apiKey = config('services.gemini.key');
        }
    }

    public function setModel(string $model): self
    {
        // Strip "models/" prefix if present
        $this->model = preg_replace('/^models\//', '', $model);
        return $this;
    }

    public function extractDataFromImage(string $imagePath, array $options = []): array
    {
        if (!$this->apiKey) {
            throw new \RuntimeException("Gemini API key is not configured.");
        }

        $fields = $options['ai_fields'] ?? [];
        $extraInstructions = $options['extra_instructions'] ?? '';

        $prompt = $this->buildPrompt($fields, $extraInstructions);
        $imageData = base64_encode(file_get_contents($imagePath));
        $mimeType = mime_content_type($imagePath);

        $url = "https://generativelanguage.googleapis.com/{$this->apiVersion}/models/{$this->model}:generateContent?key={$this->apiKey}";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $imageData,
                            ],
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json',
            ],
        ];

        try {
            $response = $this->postToGemini($url, $payload);

            if ($response->failed()) {
                $error = $response->json();
                $msg = $error['error']['message'] ?? 'Unknown API error';
                
                if ($response->status() === 429) {
                    throw new \RuntimeException("API Rate Limit Reached. Please try again later.");
                }
                
                throw new \RuntimeException("Gemini API Error: " . $msg);
            }

            $result = $response->json();
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            
            return $this->normalizeResponse(json_decode($text, true) ?: [], $fields);

        } catch (\Exception $e) {
            Log::error("Gemini Extraction Failed: " . $e->getMessage());
            throw $e;
        }
    }

    public function suggestFieldsFromImage(string $imagePath): array
    {
        if (!$this->apiKey) {
            throw new \RuntimeException("Gemini API key is not configured.");
        }

        $prompt = "Analyze this receipt image and suggest a list of data fields to extract. 
        For each field, provide:
        1. 'key': snake_case technical name
        2. 'label': Thai descriptive name
        3. 'type': one of [text, number, date, array]
        4. 'hint': brief instruction on where to find it
        
        Return ONLY a valid JSON array of objects with these keys.";

        $imageData = base64_encode(file_get_contents($imagePath));
        $mimeType = mime_content_type($imagePath);
        $url = "https://generativelanguage.googleapis.com/{$this->apiVersion}/models/{$this->model}:generateContent?key={$this->apiKey}";

        $payload = [
            'contents' => [['parts' => [['text' => $prompt], ['inline_data' => ['mime_type' => $mimeType, 'data' => $imageData]]]]],
            'generationConfig' => ['response_mime_type' => 'application/json']
        ];

        try {
            $response = $this->postToGemini($url, $payload);
            if ($response->failed()) throw new \RuntimeException("Gemini API Error");

            $result = $response->json();
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
            return json_decode($text, true) ?: [];
        } catch (\Exception $e) {
            Log::error("Gemini Suggestion Failed: " . $e->getMessage());
            throw $e;
        }
    }

    protected function postToGemini(string $url, array $payload): Response
    {
        $retryDelays = self::RETRY_DELAYS_MS;

        return Http::timeout(60)
            ->retry(
                count($retryDelays) + 1,
                function (int $attempt, \Exception $exception) use ($retryDelays): int {
                    $delay = $retryDelays[$attempt - 1] ?? $retryDelays[count($retryDelays) - 1];

                    Log::warning('Gemini request timed out, retrying.', [
                        'attempt' => $attempt + 1,
                        'delay_ms' => $delay,
                        'error' => $exception->getMessage(),
                    ]);

                    return $delay;
                },
                function (\Exception $exception): bool {
                    if (!$exception instanceof ConnectionException) {
                        return false;
                    }

                    $message = strtolower($exception->getMessage());

                    return str_contains($message, 'curl error 28')
                        || str_contains($message, 'operation timed out')
                        || str_contains($message, 'timed out');
                },
                throw: true
            )
            ->post($url, $payload);
    }

    protected function buildPrompt(array $fields, string $extra = ''): string
    {
        $schema = [];
        $hasItemsField = false;
        foreach ($fields as $f) {
            $schema[$f['key']] = $f['label'] . " (type: {$f['type']})";
            if (($f['key'] ?? '') === 'items' || ($f['type'] ?? '') === 'array') {
                $hasItemsField = true;
            }
        }

        $prompt = "As an expert OCR and document analysis engine, extract high-precision data from this Thai receipt/invoice image.
If the text is blurry or skewed, use contextual reasoning to deduce the correct value.
Handle Buddhist Year (BE) by converting to Christian Year (AD) if necessary (AD = BE - 543).
Return ONLY a valid JSON object matching this exact schema:\n";
        $prompt .= json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

        if ($hasItemsField) {
            $prompt .= "Array Standard:\n";
            $prompt .= "- If the field key is `items`, return an array of line-item objects.\n";
            $prompt .= "- Each item should use consistent keys: `name`, `qty`, `unit_price`, `total_price`, `discount`, `sku`, `note`.\n";
            $prompt .= "- Use numeric values only for `qty`, `unit_price`, `total_price`, and `discount` when possible.\n";
            $prompt .= "- If the receipt has no itemized lines, return an empty array for `items`.\n";
            $prompt .= "- Do not invent items that are not visible on the receipt.\n\n";
        }
        
        if (!empty($extra)) {
            $prompt .= "Additional Instructions: " . $extra . "\n";
        }

        $prompt .= "Rules:\n1. Accuracy is paramount. If a value is unclear, use your best judgment based on surrounding text.\n2. If a text/number/date field is absolutely not found, use null.\n3. For currency/totals, return numeric values only (e.g., 1250.50).\n4. For dates, standardize to YYYY-MM-DD. Handle Thai months correctly.\n5. For shop/store names, extract the full official name if visible.\n6. For array fields, ALWAYS return an array. Empty array [] if no items found.";
        
        return $prompt;
    }

    protected function normalizeResponse(array $data, array $fields): array
    {
        $normalized = [];
        foreach ($fields as $f) {
            $fieldKey = $f['key'];
            $value = $data[$fieldKey] ?? null;
            
            // Basic normalization based on type
            if ($f['type'] === 'number' && $value !== null) {
                $value = (float) preg_replace('/[^0-9.]/', '', (string)$value);
            }
            if ($f['type'] === 'array' && $value !== null && !is_array($value)) {
                $value = [$value];
            }
            $normalized[$fieldKey] = $value;
        }
        $normalized['__metadata'] = ['confidence_score' => 1.0, 'is_reliable' => true];
        return $normalized;
    }
}

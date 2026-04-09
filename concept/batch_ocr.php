<?php
// === batch_ocr.php ===
// Processes the /img folder and updates ocr_data.json

$config = include __DIR__ . '/config.php';
$dbFile = __DIR__ . '/ocr_data.json';
$imgDir = __DIR__ . '/img';

function loadDB() {
    global $dbFile;
    return file_exists($dbFile) ? json_decode(file_get_contents($dbFile), true) : [];
}
function saveDB($data) {
    global $dbFile;
    file_put_contents($dbFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$db = loadDB();
$images = glob($imgDir . '/*.{jpg,jpeg,png}', GLOB_BRACE);
$count = 0;

foreach ($images as $path) {
    $filename = basename($path);
    
    // Check if filename already exists in DB to avoid duplicates
    $exists = false;
    foreach($db as $entry) {
        if(isset($entry['filename']) && $entry['filename'] === $filename) {
            $exists = true; break;
        }
    }
    if($exists) continue;

    $base64 = base64_encode(file_get_contents($path));
    $url = "https://generativelanguage.googleapis.com/v1beta/models/" . $config['models'] . ":generateContent?key=" . $config['api_key'];
    
    $payload = json_encode([
        "contents" => [["parts" => [["text" => $config['prompt']], ["inline_data" => ["mime_type" => "image/jpeg", "data" => $base64]]]]],
        "generationConfig" => ["response_mime_type" => "application/json"]
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    $response = curl_exec($ch);
    curl_close($ch);

    $resData = json_decode($response, true);
    if (isset($resData['candidates'][0]['content']['parts'][0]['text'])) {
        $jsonStr = trim(preg_replace('/^```json\s*|```$/', '', $resData['candidates'][0]['content']['parts'][0]['text']));
        $extracted = json_decode($jsonStr, true);
        
        if($extracted) {
            $id = time() . '_' . uniqid();
            $db[$id] = array_merge(['id' => $id, 'filename' => $filename], $extracted);
            $count++;
        }
    }
}

saveDB($db);
echo "สำเร็จ! ประมวลผลภาพใหม่ไป $count รายการ";
?>

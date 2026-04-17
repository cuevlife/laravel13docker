<?php
// === export_excel.php (Fully Granular Edition) ===
$config = include __DIR__ . '/config.php';
$dbFile = __DIR__ . '/ocr_data.json';

if (!file_exists($dbFile)) die("ไม่มีข้อมูลให้ส่งออก");
$db = json_decode(file_get_contents($dbFile), true) ?: [];

// 1. กรองเฉพาะคอลัมน์ที่เปิดใช้งานและจัดเรียงลำดับ
$exportColumns = array_filter($config['export_columns'], function($col) {
    return $col['enabled'];
});
uasort($exportColumns, function($a, $b) { 
    return ($a['order'] ?? 99) <=> ($b['order'] ?? 99); 
});

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $config['excel_filename'] . '"');

echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
echo '<head><meta http-equiv="Content-type" content="text/html;charset=utf-8" />
<style>
    .h { background: #0f172a; color: #ffffff; font-weight: bold; border: 1px solid #333; text-align: center; }
    .num { mso-number-format:"\#\,\#\#0\.00"; text-align: right; }
    .code { mso-number-format:"\@"; text-align: center; }
    .date { mso-number-format:"yyyy\-mm\-dd"; text-align: center; }
    .text { mso-number-format:"\@"; }
</style></head><body><table border="1">';

// --- 2. สร้างหัวตาราง (Headers) ---
echo '<tr>';
foreach($exportColumns as $headerKey => $col) {
    echo '<th class="h">' . htmlspecialchars($headerKey) . '</th>';
}
echo '</tr>';

// --- 3. วนลูปข้อมูล (Data) ---
foreach(array_reverse($db) as $id => $row) {
    $items = $row['items'] ?? [['name'=>'-', 'price'=>0]];

    foreach($items as $it) {
        echo '<tr>';
        foreach($exportColumns as $headerKey => $col) {
            $source = $col['source'];
            $val = '';
            
            // ดึงค่าตามประเภท Source
            switch($source) {
                case 'shop_code':
                    $shopName = trim($row['shop_name'] ?? '');
                    $val = $config['vendor_mapping'][$shopName] ?? ''; // ถ้าไม่มี mapping ให้ปล่อยว่างหรือใส่ชื่อเดิมก็ได้
                    break;
                case 'item_code':
                    $itemName = trim($it['name'] ?? '');
                    $val = $config['item_code_mapping'][$itemName] ?? '';
                    break;
                case 'item_name':
                    $val = trim($it['name'] ?? '');
                    break;
                case 'item_price':
                    $val = $it['price'] ?? 0;
                    break;
                default:
                    // ดึงค่าตรงๆ จาก $row (เช่น date, shop_name, final_total)
                    $val = $row[$source] ?? '';
                    break;
            }

            // จัด Format ตามที่ตั้งค่าไว้
            $class = 'text';
            if($col['type'] === 'number') {
                $class = 'num';
                $val = is_numeric($val) ? (float)$val : 0;
            } elseif($col['type'] === 'date') {
                $class = 'date';
            } elseif($source === 'shop_code' || $source === 'item_code') {
                $class = 'code';
            }

            echo '<td class="'.$class.'">' . htmlspecialchars($val) . '</td>';
        }
        echo '</tr>';
    }
}

echo '</table></body></html>';
?>
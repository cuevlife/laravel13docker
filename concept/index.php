<?php
// === index.php (Enterprise Drag & Drop, CRUD Mapping, Pagination) ===
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
session_start();

$dbFile = __DIR__ . '/ocr_data.json';
$configPath = __DIR__ . '/config.php';
$config = include $configPath;
$imgDir = __DIR__ . '/img';
if (!is_dir($imgDir)) mkdir($imgDir, 0777, true);

// สร้าง Prompt สำหรับ AI
function getDynamicPrompt($config) {
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

function loadDB() {
    global $dbFile;
    if (!file_exists($dbFile)) return [];
    clearstatcache(true, $dbFile);
    return json_decode(file_get_contents($dbFile), true) ?: [];
}

function saveDB($data) {
    global $dbFile;
    return file_put_contents($dbFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

// ---------------------------------------------------------
// API HANDLERS (AJAX)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $db = loadDB();
    $action = $_POST['action'];

    if ($action === 'upload_ocr') {
        $file = $_FILES['file'];
        $fn = time() . '_' . basename($file['name']);
        $path = $imgDir . '/' . $fn;
        if (move_uploaded_file($file['tmp_name'], $path)) {
            $base64 = base64_encode(file_get_contents($path));
            $url = "https://generativelanguage.googleapis.com/v1beta/models/" . $config['models'] . ":generateContent?key=" . $config['api_key'];
            $payload = json_encode([
                "contents" => [["parts" => [
                    ["text" => getDynamicPrompt($config)], 
                    ["inline_data" => ["mime_type" => "image/jpeg", "data" => $base64]]
                ]]],
                "generationConfig" => ["response_mime_type" => "application/json"]
            ]);
            $ch = curl_init($url); curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); curl_setopt($ch, CURLOPT_POST, true); curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']); curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $res = curl_exec($ch); $resData = json_decode($res, true); curl_close($ch);

            if (isset($resData['candidates'][0]['content']['parts'][0]['text'])) {
                $ext = json_decode(trim(preg_replace('/^```json\s*|```$/', '', $resData['candidates'][0]['content']['parts'][0]['text'])), true);
                $id = 'ID_' . time() . '_' . rand(1000,9999);
                $sub = (float)($ext['subtotal'] ?? 0); $dep = (float)($ext['deposit_deduction'] ?? 0); $final = (float)($ext['final_total'] ?? 0);
                $ext['validation'] = ['math_ok' => (abs(($sub-$dep)-$final) < 0.1), 'diff' => ($sub-$dep)-$final];
                $db[$id] = array_merge(['id' => $id, 'filename' => $fn], $ext ?: []);
                saveDB($db); 
                echo json_encode(['status' => 'success']);
            } else { echo json_encode(['status' => 'error', 'message' => 'AI อ่านไฟล์ไม่สำเร็จ']); }
        }
        exit;
    }

    if ($action === 'save_edit') {
        $id = $_POST['id']; $newData = json_decode($_POST['data'], true);
        if ($newData) { $db[$id] = $newData; saveDB($db); echo json_encode(['status' => 'success']); }
        else { echo json_encode(['status' => 'error', 'message' => 'รูปแบบ JSON ไม่ถูกต้อง']); }
        exit;
    }

    if ($action === 'delete') {
        unset($db[$_POST['id']]); saveDB($db); echo json_encode(['status' => 'success']);
        exit;
    }

    // --- MAPPING CRUD ---
    if ($action === 'add_mapping') {
        $type = $_POST['map_type']; // 'vendor' or 'item'
        $textKey = trim($_POST['map_text']);
        $codeVal = trim($_POST['map_code']);
        
        if ($textKey !== '') {
            $configKey = ($type === 'vendor') ? 'vendor_mapping' : 'item_code_mapping';
            $config[$configKey][$textKey] = $codeVal;
            file_put_contents($configPath, "<?php\nreturn " . var_export($config, true) . ";");
            if(function_exists('opcache_invalidate')) opcache_invalidate($configPath, true);
        }
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($action === 'delete_mapping') {
        $type = $_POST['map_type'];
        $textKey = $_POST['map_text'];
        $configKey = ($type === 'vendor') ? 'vendor_mapping' : 'item_code_mapping';
        if (isset($config[$configKey][$textKey])) {
            unset($config[$configKey][$textKey]);
            file_put_contents($configPath, "<?php\nreturn " . var_export($config, true) . ";");
            if(function_exists('opcache_invalidate')) opcache_invalidate($configPath, true);
        }
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($action === 'save_config') {
        // Save Excel Columns (Enabled & Order)
        foreach($config['export_columns'] as $k => $v) {
            $config['export_columns'][$k]['enabled'] = isset($_POST['export_enabled'][$k]);
            if(isset($_POST['export_order'][$k])) {
                $config['export_columns'][$k]['order'] = (int)$_POST['export_order'][$k];
            }
        }
        $config['excel_filename'] = $_POST['excel_filename'] ?: 'export.xls';
        
        file_put_contents($configPath, "<?php\nreturn " . var_export($config, true) . ";");
        if(function_exists('opcache_invalidate')) opcache_invalidate($configPath, true);
        echo json_encode(['status' => 'success']);
        exit;
    }
}

$db = loadDB();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบแปลงสลิปอัจฉริยะ (Enterprise Edition)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Simple DataTables for Pagination -->
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
    <!-- SortableJS for Drag and Drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <style>
        body { background: #f0f2f5; font-family: 'Kanit', sans-serif; color: #333; }
        .app-container { max-width: 1200px; margin: 30px auto; padding: 0 15px; }
        
        .upload-hero { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border-radius: 16px; padding: 25px 20px; text-align: center; color: white; margin-bottom: 20px; cursor: pointer; transition: 0.3s; box-shadow: 0 8px 20px rgba(79, 70, 229, 0.2); }
        .upload-hero:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(79, 70, 229, 0.3); }
        .upload-icon { font-size: 36px; margin-bottom: 5px; }

        .content-box { background: white; border-radius: 16px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .nav-pills .nav-link { color: #64748b; font-weight: 500; border-radius: 8px; margin-right: 5px; }
        .nav-pills .nav-link.active { background: #0f172a; color: white; }

        .item-text { background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 13px; display: inline-block; margin: 2px; border: 1px solid #e2e8f0; }
        .item-number { color: #dc2626; font-weight: bold; }
        
        #queueContainer { display: none; margin-top: 15px; }
        .queue-item { background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 8px; margin-bottom: 5px; font-size: 14px; display: flex; justify-content: space-between; }
        
        /* Drag handle style */
        .drag-handle { cursor: grab; color: #94a3b8; font-size: 18px; padding-right: 10px; }
        .drag-handle:active { cursor: grabbing; }
        .sortable-ghost { opacity: 0.4; background-color: #f8fafc; }
        
        /* Override Datatables design to fit */
        .dataTable-table > tbody > tr > td { vertical-align: middle; }
        .mapping-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
    </style>
</head>
<body>

<div class="app-container">
    
    <!-- Main Content Box -->
    <div class="content-box">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3 flex-wrap gap-3">
            <ul class="nav nav-pills" id="mainTab">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-crud">📋 ตารางข้อมูล (ประวัติ)</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-mapping">🔀 จัดการรหัสสินค้า/ร้านค้า</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-settings">⚙️ ตั้งค่า Excel</button></li>
            </ul>
            <div class="d-flex gap-2">
                <button class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm" onclick="document.getElementById('fileInp').click()">📸 อัปโหลดสลิป</button>
                <a href="export_excel.php" class="btn btn-success fw-bold px-4 rounded-pill shadow-sm">📥 ดาวน์โหลด Excel</a>
                <input type="file" id="fileInp" multiple accept="image/*" class="d-none">
            </div>
        </div>
        
        <div id="queueContainer" class="mb-3"></div>

        <div class="tab-content">
            
            <!-- 📋 TAB: CRUD History -->
            <div class="tab-pane fade show active" id="tab-crud">
                <table class="table table-hover align-middle" id="mainHistoryTable">
                    <thead>
                        <tr>
                            <th>วันที่ / ร้าน</th>
                            <th>ลูกค้า</th>
                            <th>รายการสินค้า (ชื่อ -> รหัส)</th>
                            <th class="text-end">ยอดเงิน</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach(array_reverse($db) as $id => $row): ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($row['date'] ?? '-'); ?></div>
                                <div class="small text-muted">
                                    <?php 
                                        $sn = $row['shop_name'] ?? '-';
                                        $sc = $config['vendor_mapping'][$sn] ?? '';
                                        echo htmlspecialchars($sn);
                                        if($sc) echo ' <span class="text-primary fw-bold">['.$sc.']</span>';
                                    ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($row['customer_name'] ?? '-'); ?></td>
                            <td>
                                <?php if(!empty($row['items'])): foreach($row['items'] as $it): 
                                    $rawText = $it['name'] ?? '';
                                    $numberCode = $config['item_code_mapping'][$rawText] ?? 'ไม่มีรหัส';
                                ?>
                                    <div class="item-text">
                                        <?php echo htmlspecialchars($rawText); ?> 
                                        <span class="ms-1">></span> 
                                        <span class="item-number"><?php echo htmlspecialchars($numberCode); ?></span>
                                    </div>
                                <?php endforeach; else: echo '-'; endif; ?>
                            </td>
                            <td class="text-end fw-bold text-primary">฿<?php echo number_format($row['final_total'] ?? 0, 2); ?></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-dark rounded-pill px-3" onclick='editRow(<?php echo json_encode($id); ?>)'>แก้ไข</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- 🔀 TAB: MAPPING CRUD (DataTables) -->
            <div class="tab-pane fade" id="tab-mapping">
                <div class="row g-4">
                    <!-- Item Code Mapping -->
                    <div class="col-md-6">
                        <div class="mapping-card h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold m-0 text-danger">📦 ผังรหัสสินค้า (Item Code)</h5>
                                <button class="btn btn-sm btn-danger rounded-pill px-3 fw-bold" onclick="addMapping('item')">+ เพิ่มรหัส</button>
                            </div>
                            <p class="small text-muted">ระบบจะแปลง "ข้อความ" เป็น "รหัสตัวเลข" ให้ใน Excel โดยอัตโนมัติ</p>
                            <table class="table table-bordered table-striped" id="itemMapTable">
                                <thead><tr class="table-light"><th>ข้อความในสลิป</th><th>รหัสที่จะแปลง (Excel)</th><th width="50">ลบ</th></tr></thead>
                                <tbody>
                                    <?php foreach(($config['item_code_mapping'] ?? []) as $txt => $num): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($txt); ?></td>
                                        <td class="fw-bold text-danger"><?php echo htmlspecialchars($num); ?></td>
                                        <td class="text-center"><button class="btn btn-sm btn-outline-secondary py-0" onclick="delMap('item', '<?php echo htmlspecialchars(addslashes($txt)); ?>')">X</button></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Vendor Mapping -->
                    <div class="col-md-6">
                        <div class="mapping-card h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold m-0 text-primary">🏪 ผังรหัสร้านค้า (Vendor Code)</h5>
                                <button class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" onclick="addMapping('vendor')">+ เพิ่มรหัสร้าน</button>
                            </div>
                            <p class="small text-muted">เช่นกัน แปลงชื่อร้านค้าเป็นรหัสผู้จำหน่าย (Vendor Code)</p>
                            <table class="table table-bordered table-striped" id="vendorMapTable">
                                <thead><tr class="table-light"><th>ชื่อร้านในสลิป</th><th>รหัสร้าน (Excel)</th><th width="50">ลบ</th></tr></thead>
                                <tbody>
                                    <?php foreach(($config['vendor_mapping'] ?? []) as $txt => $num): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($txt); ?></td>
                                        <td class="fw-bold text-primary"><?php echo htmlspecialchars($num); ?></td>
                                        <td class="text-center"><button class="btn btn-sm btn-outline-secondary py-0" onclick="delMap('vendor', '<?php echo htmlspecialchars(addslashes($txt)); ?>')">X</button></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ⚙️ TAB: SETTINGS & DRAG DROP -->
            <div class="tab-pane fade" id="tab-settings">
                <form id="cfgForm">
                    <div class="p-4 border rounded-4 bg-light">
                        <h5 class="fw-bold mb-1">✅ ออกแบบคอลัมน์ Excel (Drag & Drop)</h5>
                        <p class="small text-muted mb-4">คลิกค้างที่ไอคอน ☰ แล้วลากขึ้นลงเพื่อสลับตำแหน่งคอลัมน์ในไฟล์ Excel ได้เลย!</p>
                        
                        <div class="table-responsive bg-white border rounded">
                            <table class="table table-hover align-middle m-0">
                                <thead class="table-light"><tr><th width="40"></th><th width="80" class="text-center">ออก Excel</th><th>ข้อมูลจาก AI</th><th>ชื่อหัวตาราง (Excel Header)</th><th width="80" class="text-center">ลำดับ</th></tr></thead>
                                <tbody id="sortableColumns">
                                    <?php 
                                    // Sort by current order
                                    $cols = $config['export_columns'];
                                    uasort($cols, function($a,$b) { return ($a['order']??99) <=> ($b['order']??99); });
                                    foreach($cols as $key => $col): 
                                    ?>
                                    <tr class="sortable-row">
                                        <td class="text-center"><span class="drag-handle">☰</span></td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center m-0 fs-5">
                                                <input class="form-check-input" type="checkbox" name="export_enabled[<?php echo $key; ?>]" value="1" <?php echo $col['enabled'] ? 'checked' : ''; ?>>
                                            </div>
                                        </td>
                                        <td><span class="fw-medium text-dark"><?php echo $col['label']; ?></span></td>
                                        <td><input type="text" class="form-control form-control-sm text-secondary bg-light" value="<?php echo $key; ?>" readonly style="pointer-events: none;"></td>
                                        <td class="text-center">
                                            <input type="number" name="export_order[<?php echo $key; ?>]" class="form-control form-control-sm text-center col-order-val fw-bold" value="<?php echo $col['order']; ?>" readonly>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4 col-md-6">
                            <label class="fw-bold small">ชื่อไฟล์ Excel ที่ดาวน์โหลด</label>
                            <input type="text" name="excel_filename" class="form-control" value="<?php echo htmlspecialchars($config['excel_filename']); ?>">
                        </div>
                    </div>

                    <button type="button" class="btn btn-dark btn-lg w-100 mt-4 rounded-pill fw-bold shadow" onclick="saveConfig(this)">💾 บันทึกการตั้งค่า Excel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: JSON Edit (CRUD) -->
<div class="modal fade" id="eMod" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-dark text-white rounded-top-4">
                <h5 class="modal-title fw-bold">แก้ไขข้อมูลบิล (JSON)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <textarea id="eArea" class="form-control border-0 p-4" rows="15" style="font-family: monospace; background:#f8fafc; font-size: 14px;"></textarea>
            </div>
            <div class="modal-footer bg-light border-0 rounded-bottom-4 justify-content-between">
                <button type="button" class="btn btn-outline-danger px-4 rounded-pill" onclick="delRec()">🗑️ ลบทิ้ง</button>
                <button type="button" class="btn btn-primary px-4 rounded-pill fw-bold" onclick="saveEdit()">💾 บันทึก</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const db = <?php echo json_encode($db, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>;
    let editId = null; let q = []; let isRun = false;
    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });

    // --- INIT LIBRARIES ---
    window.onload = () => {
        // Tab Memory
        const tab = localStorage.getItem('appTab');
        if(tab) {
            const btn = document.querySelector(`button[data-bs-target="${tab}"]`);
            if(btn) bootstrap.Tab.getOrCreateInstance(btn).show();
        }
        document.querySelectorAll('button[data-bs-toggle="pill"]').forEach(b => b.addEventListener('shown.bs.tab', e => localStorage.setItem('appTab', e.target.getAttribute('data-bs-target'))));

        // DataTables Pagination
        new simpleDatatables.DataTable("#mainHistoryTable", { perPage: 10, labels: { placeholder: "ค้นหาข้อมูล...", perPage: "รายการต่อหน้า", noRows: "ไม่พบข้อมูล", info: "แสดง {start} ถึง {end} จาก {rows} รายการ" } });
        new simpleDatatables.DataTable("#itemMapTable", { perPage: 5, searchable: true, labels: { placeholder: "ค้นหา..." } });
        new simpleDatatables.DataTable("#vendorMapTable", { perPage: 5, searchable: true, labels: { placeholder: "ค้นหา..." } });

        // SortableJS Drag & Drop
        new Sortable(document.getElementById('sortableColumns'), {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function () {
                // Update Order Input fields automatically after drop
                const rows = document.querySelectorAll('.sortable-row');
                rows.forEach((row, index) => {
                    row.querySelector('.col-order-val').value = index + 1;
                });
            }
        });
    };

    // --- UPLOAD LOGIC ---
    document.getElementById('fileInp').addEventListener('change', function(e) {
        const files = Array.from(e.target.files); if(!files.length) return;
        const qC = document.getElementById('queueContainer'); qC.style.display = 'block';
        files.forEach(f => {
            const id = 'q_'+Math.random().toString(36).substr(2,5);
            q.push({id, f, st:'p'});
            qC.innerHTML += `<div id="${id}" class="queue-item"><span>📄 ${f.name}</span><span id="s_${id}">รอคิว...</span></div>`;
        });
        processQ();
    });

    async function processQ() {
        if(isRun) return; const n = q.find(i=>i.st==='p');
        if(!n) { if(q.some(i=>i.st==='d')) { setTimeout(() => location.reload(), 500); } return; }
        
        isRun = true; n.st='w'; document.getElementById('s_'+n.id).innerText = 'กำลังสแกน...';
        const fd = new FormData(); fd.append('action', 'upload_ocr'); fd.append('file', n.f);
        try {
            const r = await fetch('', { method: 'POST', body: fd }); const d = await r.json();
            if(d.status === 'success') { n.st='d'; document.getElementById('s_'+n.id).innerText = 'สำเร็จ ✅'; Toast.fire({ icon: 'success', title: 'สแกนสำเร็จ' }); } 
            else { n.st='e'; document.getElementById('s_'+n.id).innerText = 'ล้มเหลว ❌'; Toast.fire({ icon: 'error', title: d.message }); }
        } catch(e) { n.st='e'; document.getElementById('s_'+n.id).innerText = 'Error ❌'; }
        isRun = false; processQ();
    }

    // --- AJAX CRUD MAPPING ---
    async function addMapping(type) {
        const { value: formValues } = await Swal.fire({
            title: type === 'item' ? 'เพิ่มรหัสสินค้า' : 'เพิ่มรหัสร้านค้า',
            html: `
                <input id="swal-input1" class="swal2-input" placeholder="${type === 'item' ? 'ชื่อสินค้าในบิล' : 'ชื่อร้านในบิล'}">
                <input id="swal-input2" class="swal2-input" placeholder="รหัสตัวเลข (สำหรับ Excel)">
            `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'บันทึก',
            cancelButtonText: 'ยกเลิก',
            preConfirm: () => {
                return [ document.getElementById('swal-input1').value, document.getElementById('swal-input2').value ]
            }
        });

        if (formValues && formValues[0] && formValues[1]) {
            const fd = new FormData();
            fd.append('action', 'add_mapping');
            fd.append('map_type', type);
            fd.append('map_text', formValues[0]);
            fd.append('map_code', formValues[1]);
            await fetch('', { method: 'POST', body: fd });
            location.reload();
        }
    }

    async function delMap(type, text) {
        const res = await Swal.fire({
            title: 'ยืนยันการลบ?',
            text: `คุณต้องการลบการผูกรหัสของ "${text}" ใช่หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ลบทิ้ง',
            cancelButtonText: 'ยกเลิก'
        });
        if(res.isConfirmed) {
            const fd = new FormData();
            fd.append('action', 'delete_mapping');
            fd.append('map_type', type);
            fd.append('map_text', text);
            await fetch('', { method: 'POST', body: fd });
            location.reload();
        }
    }

    // --- CONFIG & DB EDIT ---
    async function saveConfig(btn) {
        btn.disabled = true; btn.innerText = 'กำลังบันทึก...';
        const fd = new FormData(document.getElementById('cfgForm'));
        fd.append('action', 'save_config');
        await fetch('', { method: 'POST', body: fd });
        Toast.fire({ icon: 'success', title: 'บันทึกการตั้งค่าสำเร็จ!' }).then(() => location.reload());
    }

    const em = new bootstrap.Modal(document.getElementById('eMod'));
    function editRow(id) { editId = id; document.getElementById('eArea').value = JSON.stringify(db[id], null, 4); em.show(); }
    
    async function saveEdit() {
        const fd = new FormData(); fd.append('action', 'save_edit'); fd.append('id', editId); fd.append('data', document.getElementById('eArea').value);
        const r = await fetch('', { method: 'POST', body: fd }); const d = await r.json();
        if(d.status==='success') { Toast.fire({ icon: 'success', title: 'อัปเดตข้อมูลสำเร็จ' }).then(()=>location.reload()); } 
        else { Toast.fire({ icon: 'error', title: d.message }); }
    }
    function delRec() { 
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: "คุณแน่ใจหรือไม่ที่จะลบข้อมูลนี้ถาวร?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ลบทิ้ง',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                const fd = new FormData(); 
                fd.append('action', 'delete'); 
                fd.append('id', editId); 
                fetch('', { method: 'POST', body: fd }).then(() => location.reload()); 
            }
        });
    }
</script>
</body></html>
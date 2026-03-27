<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$slips = \App\Models\Slip::latest()->take(5)->get();
foreach($slips as $slip) {
    echo "ID: " . $slip->id . "\n";
    echo "Data: " . json_encode($slip->extracted_data, JSON_UNESCAPED_UNICODE) . "\n";
    echo "-------------------\n";
}

<?php
return array (
  'models' => 'gemini-3.1-flash-lite-preview',
  'api_key' => 'AIzaSyC7iF_-tQPu-RVIrfCFdW5TgUcUW8HfwxQ',
  'main_instruction' => 'Extract data from this receipt into JSON. Categories for items: Medicine, Service, Food, Lab/X-ray, Other.',
  'ai_fields' => 
  array (
    'date' => true,
    'shop_name' => true,
    'customer_name' => true,
    'pet_info' => true,
    'items' => true,
    'subtotal' => true,
    'deposit_deduction' => true,
    'final_total' => true,
  ),
  'export_columns' => 
  array (
    'DocDate' => 
    array (
      'enabled' => false,
      'label' => 'วันที่เอกสาร',
      'source' => 'date',
      'type' => 'date',
      'order' => 1,
    ),
    'VendorCode' => 
    array (
      'enabled' => true,
      'label' => 'รหัสร้านค้า (จาก Mapping)',
      'source' => 'shop_code',
      'type' => 'text',
      'order' => 2,
    ),
    'VendorName' => 
    array (
      'enabled' => true,
      'label' => 'ชื่อร้านค้า',
      'source' => 'shop_name',
      'type' => 'text',
      'order' => 3,
    ),
    'CustomerName' => 
    array (
      'enabled' => true,
      'label' => 'ชื่อลูกค้า',
      'source' => 'customer_name',
      'type' => 'text',
      'order' => 4,
    ),
    'PetName' => 
    array (
      'enabled' => true,
      'label' => 'สัตว์เลี้ยง',
      'source' => 'pet_info',
      'type' => 'text',
      'order' => 5,
    ),
    'ItemCode' => 
    array (
      'enabled' => true,
      'label' => 'รหัสสินค้า (จาก Mapping)',
      'source' => 'item_code',
      'type' => 'text',
      'order' => 6,
    ),
    'ItemName' => 
    array (
      'enabled' => true,
      'label' => 'ชื่อสินค้า',
      'source' => 'item_name',
      'type' => 'text',
      'order' => 7,
    ),
    'Amount' => 
    array (
      'enabled' => true,
      'label' => 'ราคาต่อหน่วย',
      'source' => 'item_price',
      'type' => 'number',
      'order' => 8,
    ),
    'Subtotal' => 
    array (
      'enabled' => false,
      'label' => 'ยอดรวมย่อย',
      'source' => 'subtotal',
      'type' => 'number',
      'order' => 9,
    ),
    'Discount' => 
    array (
      'enabled' => false,
      'label' => 'ส่วนลด/มัดจำ',
      'source' => 'deposit_deduction',
      'type' => 'number',
      'order' => 10,
    ),
    'NetTotal' => 
    array (
      'enabled' => false,
      'label' => 'ยอดสุทธิบิล',
      'source' => 'final_total',
      'type' => 'number',
      'order' => 11,
    ),
  ),
  'vendor_mapping' => 
  array (
    'คลินิกสัตว์เลี้ยง ABC' => 'V-001',
    'ร้านหมอหมา' => 'V-002',
    'MDV Solution' => 'V-MVD01',
  ),
  'item_code_mapping' => 
  array (
    'ค่าบริการ' => '1001111',
    'เจลล้างมือ' => '100112',
    'ยากิน' => 'kk0113',
    'น้ำเกลือ' => '100113',
    'Antibiotic (ยาปฏิชีวนะ)' => '1',
  ),
  'excel_filename' => 'Automate_Excel_Import.xls',
);

<?php

namespace App\Support;

class IntelligencePresets
{
    public static function all(): array
    {
        return [
            'retail' => [
                'name' => 'General Retail',
                'description' => 'Standard receipt from stores, supermarkets, etc.',
                'main_instruction' => 'Extract standard shopping data.',
                'ai_fields' => [
                    ['key' => 'shop_name', 'type' => 'string', 'label' => 'Shop Name'],
                    ['key' => 'date', 'type' => 'date', 'label' => 'Receipt Date'],
                    ['key' => 'total_amount', 'type' => 'number', 'label' => 'Total Amount'],
                    ['key' => 'tax_id', 'type' => 'string', 'label' => 'TAX ID'],
                    ['key' => 'items', 'type' => 'array', 'label' => 'Line Items'],
                ]
            ],
            'dining' => [
                'name' => 'Food & Beverage',
                'description' => 'Receipts from restaurants, cafes, bars.',
                'main_instruction' => 'Extract dining details including service charge and VAT if visible.',
                'ai_fields' => [
                    ['key' => 'shop_name', 'type' => 'string', 'label' => 'Restaurant Name'],
                    ['key' => 'date', 'type' => 'date', 'label' => 'Visit Date'],
                    ['key' => 'total_amount', 'type' => 'number', 'label' => 'Total Paid'],
                    ['key' => 'service_charge', 'type' => 'number', 'label' => 'Service Charge'],
                    ['key' => 'table_number', 'type' => 'string', 'label' => 'Table #'],
                    ['key' => 'items', 'type' => 'array', 'label' => 'Line Items'],
                ]
            ],
            'transport' => [
                'name' => 'Transport & Logistics',
                'description' => 'Tickets, parking, fuel, or shipping receipts.',
                'main_instruction' => 'Extract travel or logistics data.',
                'ai_fields' => [
                    ['key' => 'shop_name', 'type' => 'string', 'label' => 'Provider Name'],
                    ['key' => 'date', 'type' => 'date', 'label' => 'Date'],
                    ['key' => 'total_amount', 'type' => 'number', 'label' => 'Amount Paid'],
                    ['key' => 'vehicle_plate', 'type' => 'string', 'label' => 'Vehicle Plate'],
                    ['key' => 'origin_destination', 'type' => 'string', 'label' => 'Route'],
                ]
            ],
        ];
    }

    public static function detectType(string $text): string
    {
        $text = strtolower($text);
        if (str_contains($text, 'restaurant') || str_contains($text, 'cafe') || str_contains($text, 'food') || str_contains($text, 'beverage')) return 'dining';
        if (str_contains($text, 'taxi') || str_contains($text, 'fuel') || str_contains($text, 'parking') || str_contains($text, 'shipping')) return 'transport';
        return 'retail';
    }
}

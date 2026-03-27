<?php

namespace App\Exports;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SlipSheetExport implements FromArray, ShouldAutoSize, WithHeadings, WithTitle, WithEvents
{
    public function __construct(
        protected array $headings,
        protected array $rows,
        protected string $title
    ) {
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        $title = preg_replace('/[\[\]\:\*\?\/\\\\]/', '-', $this->title) ?: 'Sheet';

        return Str::limit($title, 31, '');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // If there's no data, skip styling
                if (empty($this->headings)) return;

                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                // 1. Add AutoFilter capabilities to Excel
                $sheet->setAutoFilter('A1:' . $highestColumn . $highestRow);

                // 2. Add Premium Header Styling (Discord Green & Bold)
                $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF23A559'] // Premium Discord Green
                    ],
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FF1F8B4C'],
                        ]
                    ]
                ]);
            },
        ];
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SlipWorkbookExport implements WithMultipleSheets
{
    public function __construct(
        protected array $sheets
    ) {
    }

    public function sheets(): array
    {
        return array_map(
            fn (array $sheet) => new SlipSheetExport(
                $sheet['headings'] ?? [],
                $sheet['rows'] ?? [],
                $sheet['title'] ?? 'Sheet'
            ),
            $this->sheets
        );
    }
}

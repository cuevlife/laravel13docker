<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SlipWorkbookExport implements WithMultipleSheets
{
    protected array $sheets;

    public function __construct(array $sheets)
    {
        $this->sheets = $sheets;
    }

    public function sheets(): array
    {
        $sheetExports = [];
        foreach ($this->sheets as $sheet) {
            $sheetExports[] = new SlipWorksheetExport($sheet['title'], $sheet['headings'], $sheet['rows']);
        }
        return $sheetExports;
    }
}

class SlipWorksheetExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected string $title;
    protected array $headings;
    protected array $rows;

    public function __construct(string $title, array $headings, array $rows)
    {
        $this->title = $title;
        $this->headings = $headings;
        $this->rows = $rows;
    }

    public function collection()
    {
        return collect($this->rows);
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return $this->title;
    }
}

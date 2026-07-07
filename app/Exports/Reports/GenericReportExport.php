<?php

namespace App\Exports\Reports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    ShouldAutoSize,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
};
use PhpOffice\PhpSpreadsheet\Style\{Alignment, Fill};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GenericReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private readonly string $title,
        private readonly Collection $rows,
        private readonly array $headings,
        private readonly array $keys,
    ) {
    }

    public function title(): string
    {
        return mb_substr($this->title, 0, 31);
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return array_merge(['#'], $this->headings);
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        $values = collect($this->keys)->map(function ($key) use ($row) {
            if (is_array($row)) {
                return $row[$key] ?? '';
            }

            return $row->{$key} ?? '';
        })->all();

        return array_merge([$i], $values);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1A56DB']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}

<?php

namespace App\Exports;

use App\Services\AttendanceService;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\{Alignment, Fill};

class AttendanceReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private readonly int $classId,
        private readonly int $month,
        private readonly int $year,
    ) {
    }

    public function title(): string
    {
        return 'Rekap Absensi ' . date('F Y', mktime(0, 0, 0, $this->month, 1, $this->year));
    }

    public function collection()
    {
        return (new AttendanceService())->getMonthlyRecap($this->classId, $this->month, $this->year);
    }

    public function headings(): array
    {
        return ['#', 'NIS', 'Nama Siswa', 'Hadir', 'Sakit', 'Izin', 'Alfa', 'Terlambat', 'Total HE', '% Kehadiran'];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;
        return [
            $i,
            $row['student']->nis,
            $row['student']->name,
            $row['hadir'],
            $row['sakit'],
            $row['izin'],
            $row['alfa'],
            $row['terlambat'],
            $row['total_he'],
            $row['percentage'] . '%',
        ];
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
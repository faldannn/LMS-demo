<?php

namespace App\Exports;

use App\Models\Quiz;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuizResultsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(private readonly Quiz $quiz, private readonly Collection $recap)
    {
    }

    public function collection(): Collection
    {
        return $this->recap;
    }

    public function headings(): array
    {
        return ['No', 'NIS', 'Nama Siswa', 'Status', 'Waktu Mulai', 'Waktu Selesai', 'Nilai', 'Lulus', 'Perlu Koreksi'];
    }

    public function map($row): array
    {
        static $no = 0;
        $attempt = $row['attempt'];

        return [
            ++$no,
            $row['student']->nis,
            $row['student']->name,
            $attempt ? ($attempt->status === 'auto_submitted' ? 'Auto Submit' : ucfirst($attempt->status)) : 'Belum',
            $attempt?->start_time?->format('d/m/Y H:i') ?? '-',
            $attempt?->end_time?->format('d/m/Y H:i') ?? '-',
            $attempt && $attempt->isFinished() ? (float) $attempt->score : '-',
            $row['passed'] ? 'Ya' : 'Tidak',
            $attempt?->needs_grading ? 'Ya' : 'Tidak',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF15803D']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}

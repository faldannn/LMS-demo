<?php

namespace App\Exports;

use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    WithMultipleSheets,
    WithStyles,
    WithTitle,
    ShouldAutoSize
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\{Alignment, Fill};

class TeacherAttendanceExport implements WithMultipleSheets
{
    public function __construct(
        private readonly string $period = 'month',
        private readonly ?string $date = null,
        private readonly ?string $month = null,
        private readonly ?string $startDate = null,
        private readonly ?string $endDate = null,
    ) {
    }

    public function sheets(): array
    {
        return [
            new TeacherAttendanceSummarySheet($this->period, $this->date, $this->month, $this->startDate, $this->endDate),
            new TeacherAttendanceLogsSheet($this->period, $this->date, $this->month, $this->startDate, $this->endDate),
        ];
    }
}

class TeacherAttendanceSummarySheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private readonly string $period,
        private readonly ?string $date,
        private readonly ?string $month,
        private readonly ?string $startDate,
        private readonly ?string $endDate,
    ) {
    }

    public function title(): string
    {
        return 'Ringkasan Seluruh Guru';
    }

    public function collection()
    {
        $teachers = Teacher::active()->orderBy('name')->get();
        [$sDate, $eDate] = $this->resolveDateRange();

        $attendances = TeacherAttendance::whereBetween('date', [$sDate->toDateString(), $eDate->toDateString()])->get();

        return $teachers->map(function (Teacher $teacher) use ($attendances) {
            $tLogs = $attendances->where('teacher_id', $teacher->id);
            $hadir = $tLogs->where('status', 'hadir')->count();
            $terlambat = $tLogs->where('status', 'terlambat')->count();
            $sakit = $tLogs->where('status', 'sakit')->count();
            $izin = $tLogs->where('status', 'izin')->count();
            $alfa = $tLogs->where('status', 'alfa')->count();
            $total = $tLogs->count();

            return [
                'nip' => $teacher->nip ?? '-',
                'name' => $teacher->name,
                'subject' => $teacher->subject ?? '-',
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'sakit' => $sakit,
                'izin' => $izin,
                'alfa' => $alfa,
                'total' => $total,
            ];
        });
    }

    public function headings(): array
    {
        return [
            '#',
            'NIP',
            'Nama Guru',
            'Mata Pelajaran',
            'Hadir',
            'Terlambat',
            'Sakit',
            'Izin',
            'Alfa',
            'Total Presensi',
        ];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $row['nip'],
            $row['name'],
            $row['subject'],
            $row['hadir'],
            $row['terlambat'],
            $row['sakit'],
            $row['izin'],
            $row['alfa'],
            $row['total'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1D4ED8']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    private function resolveDateRange(): array
    {
        if ($this->startDate && $this->endDate) {
            return [Carbon::parse($this->startDate), Carbon::parse($this->endDate)];
        }

        if ($this->period === 'today') {
            $today = today();
            return [$today, $today];
        }

        if ($this->period === 'week') {
            $anchor = $this->date ? Carbon::parse($this->date) : today();
            return [$anchor->copy()->startOfWeek(), $anchor->copy()->endOfWeek()];
        }

        if ($this->period === '30_days' || $this->period === 'last_30_days') {
            return [today()->subDays(30), today()];
        }

        if ($this->period === 'month') {
            return [today()->startOfMonth(), today()->endOfMonth()];
        }

        if ($this->date) {
            $d = Carbon::parse($this->date);
            return [$d, $d];
        }

        return [today()->startOfMonth(), today()->endOfMonth()];
    }
}

class TeacherAttendanceLogsSheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private readonly string $period,
        private readonly ?string $date,
        private readonly ?string $month,
        private readonly ?string $startDate,
        private readonly ?string $endDate,
    ) {
    }

    public function title(): string
    {
        return 'Log Rincian Harian';
    }

    public function collection()
    {
        [$sDate, $eDate] = $this->resolveDateRange();

        $attendances = TeacherAttendance::with(['teacher', 'user'])
            ->whereBetween('date', [$sDate->toDateString(), $eDate->toDateString()])
            ->orderBy('date', 'desc')
            ->get();

        return $attendances->map(function (TeacherAttendance $att) {
            $teacher = $att->teacher;

            return [
                'date' => $att->date ? $att->date->translatedFormat('d/m/Y') : '-',
                'nip' => $teacher?->nip ?? '-',
                'name' => $teacher?->name ?? '-',
                'subject' => $teacher?->subject ?? '-',
                'status' => $att->status_label,
                'check_in' => $att->check_in_at ? $att->check_in_at->format('H:i:s') . ' WIB' : '-',
                'check_out' => $att->check_out_at ? $att->check_out_at->format('H:i:s') . ' WIB' : '-',
                'location' => ($att->check_in_lat && $att->check_in_lng) 
                    ? $att->check_in_lat . ', ' . $att->check_in_lng 
                    : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            '#',
            'Tanggal Absensi',
            'NIP',
            'Nama Guru',
            'Mata Pelajaran',
            'Status Kehadiran',
            'Jam Absen Masuk',
            'Jam Absen Pulang',
            'Koordinat GPS',
        ];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $row['date'],
            $row['nip'],
            $row['name'],
            $row['subject'],
            $row['status'],
            $row['check_in'],
            $row['check_out'],
            $row['location'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2563EB']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    private function resolveDateRange(): array
    {
        if ($this->startDate && $this->endDate) {
            return [Carbon::parse($this->startDate), Carbon::parse($this->endDate)];
        }

        if ($this->period === 'today') {
            $today = today();
            return [$today, $today];
        }

        if ($this->period === 'week') {
            $anchor = $this->date ? Carbon::parse($this->date) : today();
            return [$anchor->copy()->startOfWeek(), $anchor->copy()->endOfWeek()];
        }

        if ($this->period === '30_days' || $this->period === 'last_30_days') {
            return [today()->subDays(30), today()];
        }

        if ($this->period === 'month') {
            return [today()->startOfMonth(), today()->endOfMonth()];
        }

        if ($this->date) {
            $d = Carbon::parse($this->date);
            return [$d, $d];
        }

        return [today()->startOfMonth(), today()->endOfMonth()];
    }
}

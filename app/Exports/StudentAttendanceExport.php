<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\Classes;
use App\Models\Student;
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

class StudentAttendanceExport implements WithMultipleSheets
{
    public function __construct(
        private readonly string $period = 'month',
        private readonly ?string $date = null,
        private readonly ?string $month = null,
        private readonly ?int $classId = null,
        private readonly ?string $startDate = null,
        private readonly ?string $endDate = null,
    ) {
    }

    public function sheets(): array
    {
        if ($this->classId) {
            return [
                new StudentAttendanceSummarySheet($this->period, $this->date, $this->month, $this->classId, $this->startDate, $this->endDate),
                new StudentAttendanceLogsSheet($this->period, $this->date, $this->month, $this->classId, $this->startDate, $this->endDate),
            ];
        }

        // Bulk Export: 1 Sheet Summary + 1 Sheet per Class
        $sheets = [];
        $sheets[] = new StudentAttendanceSummarySheet($this->period, $this->date, $this->month, null, $this->startDate, $this->endDate);

        $classes = Classes::orderBy('name')->get();
        foreach ($classes as $classItem) {
            $sheets[] = new SingleClassCombinedSheet(
                $classItem->id,
                $classItem->name,
                $this->period,
                $this->date,
                $this->month,
                $this->startDate,
                $this->endDate
            );
        }

        return $sheets;
    }
}

class StudentAttendanceSummarySheet implements
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
        private readonly ?int $classId,
        private readonly ?string $startDate,
        private readonly ?string $endDate,
    ) {
    }

    public function title(): string
    {
        return $this->classId ? 'Rekapitulasi Siswa' : 'Ringkasan Semua Kelas';
    }

    public function collection()
    {
        $query = Student::with('class')->active()->orderBy('name');
        if ($this->classId) {
            $query->where('class_id', $this->classId);
        }
        $students = $query->get();

        [$sDate, $eDate] = $this->resolveDateRange();

        $attendances = Attendance::with('details')
            ->when($this->classId, fn ($q) => $q->where('class_id', $this->classId))
            ->whereBetween('date', [$sDate->toDateString(), $eDate->toDateString()])
            ->whereIn('session', ['pagi', 'siang', 'harian'])
            ->get();

        $allDetails = $attendances->flatMap->details;
        $periodeLabel = $sDate->format('d/m/Y') . ' - ' . $eDate->format('d/m/Y');

        return $students->map(function (Student $student) use ($allDetails, $periodeLabel) {
            $studentDetails = $allDetails->where('student_id', $student->id);
            $hadir = $studentDetails->where('status', 'hadir')->count();
            $terlambat = $studentDetails->where('status', 'terlambat')->count();
            $izin = $studentDetails->where('status', 'izin')->count();
            $sakit = $studentDetails->where('status', 'sakit')->count();
            $alfa = $studentDetails->where('status', 'alfa')->count();
            $total = $studentDetails->count();
            $presentCount = $hadir + $terlambat;
            $percentage = $total > 0 ? round($presentCount / $total * 100, 1) : 0;

            return [
                'periode' => $periodeLabel,
                'nis' => $student->nis,
                'name' => $student->name,
                'class_name' => $student->class?->name ?? '-',
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'alfa' => $alfa,
                'total' => $total,
                'percentage' => $percentage . '%',
            ];
        });
    }

    public function headings(): array
    {
        return [
            '#',
            'Periode Tanggal',
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Hadir',
            'Terlambat',
            'Izin',
            'Sakit',
            'Alfa (Tanpa Keterangan)',
            'Total Pertemuan',
            '% Kehadiran',
        ];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $row['periode'],
            $row['nis'],
            $row['name'],
            $row['class_name'],
            $row['hadir'],
            $row['terlambat'],
            $row['izin'],
            $row['sakit'],
            $row['alfa'],
            $row['total'],
            $row['percentage'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF10B981']],
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
            $schedQuery = \App\Models\Schedule::query()->where('day_of_week', 6);
            if ($this->classId) {
                $schedQuery->where('class_id', $this->classId);
            }
            $hasSaturday = $schedQuery->exists();
            $endDay = $hasSaturday ? Carbon::SATURDAY : Carbon::FRIDAY;
            return [$anchor->copy()->startOfWeek(Carbon::MONDAY), $anchor->copy()->endOfWeek($endDay)];
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

class SingleClassCombinedSheet implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private readonly int $classId,
        private readonly string $className,
        private readonly string $period,
        private readonly ?string $date,
        private readonly ?string $month,
        private readonly ?string $startDate,
        private readonly ?string $endDate,
    ) {
    }

    public function title(): string
    {
        $safeName = str_replace(['\\', '/', '?', '*', ':', '[', ']'], '', $this->className);
        return substr($safeName, 0, 31) ?: 'Kelas';
    }

    public function collection()
    {
        [$sDate, $eDate] = $this->resolveDateRange();
        $periodeLabel = $sDate->format('d/m/Y') . ' - ' . $eDate->format('d/m/Y');

        $students = Student::where('class_id', $this->classId)->active()->orderBy('name')->get();

        $attendances = Attendance::with('details')
            ->where('class_id', $this->classId)
            ->whereBetween('date', [$sDate->toDateString(), $eDate->toDateString()])
            ->whereIn('session', ['pagi', 'siang', 'harian'])
            ->get();

        $allDetails = $attendances->flatMap->details;

        $rows = collect();

        // 1. Header Info
        $rows->push(['REKAPITULASI PRESENSI SISWA - KELAS ' . strtoupper($this->className)]);
        $rows->push(['Periode: ' . $periodeLabel]);
        $rows->push(['']);

        // 2. Table 1: Summary Headings
        $rows->push([
            '#',
            'NIS',
            'Nama Siswa',
            'Hadir',
            'Terlambat',
            'Izin',
            'Sakit',
            'Alfa (Tanpa Keterangan)',
            'Total Pertemuan',
            '% Kehadiran',
        ]);

        $num = 0;
        foreach ($students as $student) {
            $num++;
            $studentDetails = $allDetails->where('student_id', $student->id);
            $hadir = $studentDetails->where('status', 'hadir')->count();
            $terlambat = $studentDetails->where('status', 'terlambat')->count();
            $izin = $studentDetails->where('status', 'izin')->count();
            $sakit = $studentDetails->where('status', 'sakit')->count();
            $alfa = $studentDetails->where('status', 'alfa')->count();
            $total = $studentDetails->count();
            $presentCount = $hadir + $terlambat;
            $percentage = $total > 0 ? round($presentCount / $total * 100, 1) : 0;

            $rows->push([
                $num,
                $student->nis,
                $student->name,
                $hadir,
                $terlambat,
                $izin,
                $sakit,
                $alfa,
                $total,
                $percentage . '%',
            ]);
        }

        $rows->push(['']);
        $rows->push(['']);

        // 3. Table 2: Logs Headings
        $rows->push(['LOG RINCIAN HARIAN ABSENSI SISWA']);
        $rows->push([
            '#',
            'Tanggal Absensi',
            'NIS',
            'Nama Siswa',
            'Status Kehadiran',
            'Jam Masuk',
            'Keterangan / Alasan',
        ]);

        $logsQuery = AttendanceDetail::with(['attendance', 'student'])
            ->whereHas('attendance', function ($q) use ($sDate, $eDate) {
                $q->where('class_id', $this->classId)
                  ->whereBetween('date', [$sDate->toDateString(), $eDate->toDateString()])
                  ->whereIn('session', ['pagi', 'siang', 'harian']);
            })
            ->get()
            ->sortBy([
                fn ($a) => $a->attendance->date->toDateString(),
                fn ($a) => $a->student?->name ?? '',
            ]);

        $logNum = 0;
        foreach ($logsQuery as $detail) {
            $logNum++;
            $student = $detail->student;
            $att = $detail->attendance;

            $rows->push([
                $logNum,
                $att?->date ? $att->date->translatedFormat('d/m/Y') : '-',
                $student?->nis ?? '-',
                $student?->name ?? '-',
                $detail->status_label,
                $detail->check_in ? $detail->check_in->format('H:i') : '-',
                $detail->notes ?: '-',
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14],
            ],
            2 => [
                'font' => ['italic' => true, 'color' => ['argb' => 'FF475569']],
            ],
            4 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF10B981']],
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
            $schedQuery = \App\Models\Schedule::query()->where('day_of_week', 6)->where('class_id', $this->classId);
            $hasSaturday = $schedQuery->exists();
            $endDay = $hasSaturday ? Carbon::SATURDAY : Carbon::FRIDAY;
            return [$anchor->copy()->startOfWeek(Carbon::MONDAY), $anchor->copy()->endOfWeek($endDay)];
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

class StudentAttendanceLogsSheet implements
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
        private readonly ?int $classId,
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

        $query = AttendanceDetail::with(['attendance.class', 'student'])
            ->whereHas('attendance', function ($q) use ($sDate, $eDate) {
                $q->whereBetween('date', [$sDate->toDateString(), $eDate->toDateString()]);
                if ($this->classId) {
                    $q->where('class_id', $this->classId);
                }
                $q->whereIn('session', ['pagi', 'siang', 'harian']);
            })
            ->get();

        return $query->sortBy([
            fn ($a) => $a->attendance->date->toDateString(),
            fn ($a) => $a->student?->name ?? '',
        ])->map(function (AttendanceDetail $detail) {
            $student = $detail->student;
            $attendance = $detail->attendance;

            return [
                'date' => $attendance?->date ? $attendance->date->translatedFormat('d/m/Y') : '-',
                'nis' => $student?->nis ?? '-',
                'name' => $student?->name ?? '-',
                'class_name' => $attendance?->class?->name ?? '-',
                'status' => $detail->status_label,
                'check_in' => $detail->check_in ? $detail->check_in->format('H:i') : '-',
                'notes' => $detail->notes ?: '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            '#',
            'Tanggal Absensi',
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Status Kehadiran',
            'Jam Masuk',
            'Keterangan / Alasan',
        ];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $row['date'],
            $row['nis'],
            $row['name'],
            $row['class_name'],
            $row['status'],
            $row['check_in'],
            $row['notes'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF059669']],
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
            $schedQuery = \App\Models\Schedule::query()->where('day_of_week', 6);
            if ($this->classId) {
                $schedQuery->where('class_id', $this->classId);
            }
            $hasSaturday = $schedQuery->exists();
            $endDay = $hasSaturday ? Carbon::SATURDAY : Carbon::FRIDAY;
            return [$anchor->copy()->startOfWeek(Carbon::MONDAY), $anchor->copy()->endOfWeek($endDay)];
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

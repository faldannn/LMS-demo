<?php
namespace App\Services;

use App\Models\{Attendance, AttendanceDetail, Classes};
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getDailyReport(string $date, ?int $classId = null): array
    {
        $query = Attendance::with(['class', 'teacher', 'details.student'])
            ->whereDate('date', $date);

        if ($classId)
            $query->where('class_id', $classId);

        $attendances = $query->get();

        $summary = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alfa' => 0,
            'terlambat' => 0,
            'total' => 0,
        ];

        foreach ($attendances as $att) {
            foreach ($att->details as $d) {
                $summary[$d->status]++;
                $summary['total']++;
            }
        }

        $summary['percentage'] = $summary['total'] > 0
            ? round(($summary['hadir'] + $summary['terlambat']) / $summary['total'] * 100, 1)
            : 0;

        return compact('attendances', 'summary');
    }

    public function getMonthlyReport(int $month, int $year, ?int $classId = null): array
    {
        $query = DB::table('attendance_details as ad')
            ->join('attendance as a', 'a.id', '=', 'ad.attendance_id')
            ->join('classes as c', 'c.id', '=', 'a.class_id')
            ->select(
                'c.id as class_id',
                'c.name as class_name',
                DB::raw('COUNT(DISTINCT a.id) as sessions'),
                DB::raw('COUNT(ad.id) as total'),
                DB::raw("SUM(ad.status='hadir') as hadir"),
                DB::raw("SUM(ad.status='sakit') as sakit"),
                DB::raw("SUM(ad.status='izin') as izin"),
                DB::raw("SUM(ad.status='alfa') as alfa"),
                DB::raw("SUM(ad.status='terlambat') as terlambat"),
            )
            ->whereMonth('a.date', $month)
            ->whereYear('a.date', $year)
            ->groupBy('c.id', 'c.name')
            ->orderBy('c.name');

        if ($classId)
            $query->where('a.class_id', $classId);

        $rows = $query->get()->map(function ($row) {
            $row->percentage = $row->total > 0
                ? round(($row->hadir + $row->terlambat) / $row->total * 100, 1)
                : 0;
            return $row;
        });

        return ['rows' => $rows, 'month' => $month, 'year' => $year];
    }

    public function getSchoolSummary(): array
    {
        $totalStudents = \App\Models\Student::active()->count();
        $monthly = $this->getMonthlyReport(now()->month, now()->year);
        $avgPct = $monthly['rows']->avg('percentage') ?? 0;

        return [
            'total_students'  => $totalStudents,
            'avg_attendance'  => round($avgPct, 1),
            'avg_percentage'  => round($avgPct, 1),
            'total_hadir'     => (int) $monthly['rows']->sum('hadir'),
            'total_alfa'      => (int) $monthly['rows']->sum('alfa'),
            'classes_done'    => $monthly['rows']->count(),
        ];
    }
}

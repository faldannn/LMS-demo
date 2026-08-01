<?php

namespace App\Services\Reports;

use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Teacher;
use App\Support\ReportFilter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ComprehensiveReportService
{
    /**
     * Rekap kehadiran guru: berapa sesi yang diinput per guru per periode.
     */
    public function teacherAttendance(ReportFilter $filter): array
    {
        $query = Attendance::query()
            ->with('teacher', 'class');

        if ($filter->dateFrom) {
            $query->whereDate('date', '>=', $filter->dateFrom);
        }
        if ($filter->dateTo) {
            $query->whereDate('date', '<=', $filter->dateTo);
        }
        if ($filter->classId) {
            $query->where('class_id', $filter->classId);
        }

        $attendances = $query->get();

        $rows = $attendances
            ->groupBy('teacher_id')
            ->map(function (Collection $group) {
                /** @var \App\Models\Attendance $first */
                $first   = $group->first();
                $teacher = $first->teacher;

                return [
                    'teacher_id' => $first->teacher_id,
                    'name'       => $teacher?->name ?? '—',
                    'nip'        => $teacher?->nip  ?? '—',
                    'sessions'   => $group->count(),
                    'pagi'       => $group->where('session', 'pagi')->count(),
                    'siang'      => $group->where('session', 'siang')->count(),
                    'sore'       => $group->where('session', 'sore')->count(),
                ];
            })
            ->sortByDesc('sessions')
            ->values();

        return [
            'rows'       => $rows,
            'total'      => $rows->sum('sessions'),
            'date_from'  => $filter->dateFrom,
            'date_to'    => $filter->dateTo,
        ];
    }

    /**
     * Rekap kehadiran siswa: hadir / izin / sakit / alfa per siswa per periode.
     */
    public function studentAttendance(ReportFilter $filter): array
    {
        $query = AttendanceDetail::query()
            ->with(['student', 'attendance'])
            ->join('attendance', 'attendance.id', '=', 'attendance_details.attendance_id');

        if ($filter->dateFrom) {
            $query->whereDate('attendance.date', '>=', $filter->dateFrom);
        }
        if ($filter->dateTo) {
            $query->whereDate('attendance.date', '<=', $filter->dateTo);
        }
        if ($filter->classId) {
            $query->where('attendance.class_id', $filter->classId);
        }

        $details = $query->select('attendance_details.*')->get();

        $rows = $details
            ->groupBy('student_id')
            ->map(function (Collection $group) {
                /** @var \App\Models\AttendanceDetail $first */
                $first   = $group->first();
                $student = $first->student;

                return [
                    'student_id' => $first->student_id,
                    'nis'        => $student?->nis  ?? '—',
                    'name'       => $student?->name ?? '—',
                    'class'      => $student?->class?->name ?? '—',
                    'hadir'      => $group->where('status', 'hadir')->count(),
                    'terlambat'  => $group->where('status', 'terlambat')->count(),
                    'sakit'      => $group->where('status', 'sakit')->count(),
                    'izin'       => $group->where('status', 'izin')->count(),
                    'alfa'       => $group->where('status', 'alfa')->count(),
                    'total'      => $group->count(),
                ];
            })
            ->sortBy('name')
            ->values();

        return [
            'rows'      => $rows,
            'date_from' => $filter->dateFrom,
            'date_to'   => $filter->dateTo,
        ];
    }

    /**
     * Rekap nilai tugas per siswa (stub — bisa dikembangkan).
     */
    public function grades(ReportFilter $filter): array
    {
        return [
            'rows'      => collect(),
            'date_from' => $filter->dateFrom,
            'date_to'   => $filter->dateTo,
        ];
    }

    /**
     * Rekap aktivitas guru (materi, tugas, absensi yang diinput).
     */
    public function teacherActivity(ReportFilter $filter): array
    {
        return [
            'rows'      => collect(),
            'date_from' => $filter->dateFrom,
            'date_to'   => $filter->dateTo,
        ];
    }

    /**
     * Rekap aktivitas siswa (pengumpulan tugas, presensi mandiri, dll).
     */
    public function studentActivity(ReportFilter $filter): array
    {
        return [
            'rows'      => collect(),
            'date_from' => $filter->dateFrom,
            'date_to'   => $filter->dateTo,
        ];
    }

    /**
     * Ringkasan konten pembelajaran per kelas dan mata pelajaran.
     */
    public function learning(ReportFilter $filter): array
    {
        return [
            'rows'      => collect(),
            'date_from' => $filter->dateFrom,
            'date_to'   => $filter->dateTo,
        ];
    }

    /**
     * Dispatch ke method yang sesuai berdasarkan tipe laporan.
     */
    public function forType(string $type, ReportFilter $filter): array
    {
        $method = match ($type) {
            'teacher-attendance' => 'teacherAttendance',
            'student-attendance' => 'studentAttendance',
            'grades'             => 'grades',
            'teacher-activity'   => 'teacherActivity',
            'student-activity'   => 'studentActivity',
            'learning'           => 'learning',
            default              => throw new \InvalidArgumentException("Tipe laporan tidak dikenal: {$type}"),
        };

        return $this->{$method}($filter);
    }
}

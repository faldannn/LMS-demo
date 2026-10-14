<?php

namespace App\Http\Controllers;

use App\Models\SelfAttendanceLog;

class SelfAttendanceController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        abort_unless($student, 403, 'Hanya siswa yang dapat mengakses presensi mandiri.');

        $todayLog = SelfAttendanceLog::where('student_id', $student->id)
            ->where('attendance_date', today())
            ->where('status', 'success')
            ->first();

        $session = $student->class?->session ?? 'pagi';
        $checkinStart = $session === 'siang'
            ? config('attendance.student_checkin_start_siang', '11:30:00')
            : config('attendance.student_checkin_start', '06:00:00');

        $sampleClassQr = 'QR-CLASS-' . ($student->class_id ?? 1) . '-' . today()->toDateString();

        return view('attendance.self-checkin', compact('student', 'todayLog', 'checkinStart', 'session', 'sampleClassQr'));
    }

    public function logs()
    {
        $this->authorizeStaff();

        $logs = SelfAttendanceLog::with(['student.class', 'attendanceDetail'])
            ->latest('checked_in_at')
            ->paginate(25);

        return view('attendance.self-logs', compact('logs'));
    }

    private function authorizeStaff(): void
    {
        abort_unless(auth()->user()->isStaff(), 403);
    }
}

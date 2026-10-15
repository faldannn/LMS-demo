<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\Classes;
use App\Models\SelfAttendanceLog;
use App\Models\Student;
use App\Services\QrAttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SelfAttendanceController extends Controller
{
    /**
     * Tampilan Kartu QR Siswa (Hanya menampilkan Kode QR siswa)
     */
    public function index()
    {
        $student = auth()->user()->student;
        abort_unless($student, 403, 'Hanya siswa yang dapat mengakses kartu presensi.');

        $todayLog = SelfAttendanceLog::where('student_id', $student->id)
            ->where('attendance_date', today())
            ->where('status', 'success')
            ->first();

        // Cek juga di AttendanceDetail jika dicatat via sistem manual/guru
        if (!$todayLog) {
            $todayDetail = AttendanceDetail::whereHas('attendance', fn ($q) => $q->where('date', today()->toDateString()))
                ->where('student_id', $student->id)
                ->where('status', 'hadir')
                ->latest('id')
                ->first();

            if ($todayDetail) {
                $todayLog = (object) [
                    'checked_in_at' => $todayDetail->check_in ? \Carbon\Carbon::parse($todayDetail->check_in) : now(),
                    'mode_label' => 'Scan QR Guru',
                    'notes' => $todayDetail->notes ?: 'Tercatat Hadir',
                    'attendance_date' => today(),
                ];
            }
        }

        $session = $student->class?->session ?? 'pagi';
        $checkinStart = $session === 'siang'
            ? config('attendance.student_checkin_start_siang', '11:30:00')
            : config('attendance.student_checkin_start', '06:00:00');

        return view('attendance.self-checkin', compact('student', 'todayLog', 'checkinStart', 'session'));
    }

    /**
     * Polling status presensi siswa (dipanggil background dari halaman kartu siswa)
     */
    public function checkStatusPoll(): JsonResponse
    {
        $student = auth()->user()->student;
        if (!$student) {
            return response()->json(['checked_in' => false]);
        }

        $log = SelfAttendanceLog::where('student_id', $student->id)
            ->where('attendance_date', today())
            ->where('status', 'success')
            ->first();

        if ($log) {
            return response()->json([
                'checked_in' => true,
                'time' => $log->checked_in_at ? $log->checked_in_at->format('H:i:s') . ' WIB' : 'Hari ini',
                'notes' => $log->notes ?: 'Discan oleh Guru / Admin',
            ]);
        }

        $detail = AttendanceDetail::whereHas('attendance', fn ($q) => $q->where('date', today()->toDateString()))
            ->where('student_id', $student->id)
            ->where('status', 'hadir')
            ->latest('id')
            ->first();

        if ($detail) {
            return response()->json([
                'checked_in' => true,
                'time' => $detail->check_in ? substr($detail->check_in, 0, 5) . ' WIB' : 'Hari ini',
                'notes' => $detail->notes ?: 'Hadir via Scan Guru',
            ]);
        }

        return response()->json(['checked_in' => false]);
    }

    /**
     * Halaman Scanner QR Siswa untuk Guru & Admin
     */
    public function scanner(Request $request)
    {
        $this->authorizeStaff();

        $classes = Classes::orderBy('name')->get(['id', 'name', 'session']);
        
        $recentLogs = SelfAttendanceLog::with(['student.class'])
            ->where('attendance_date', today())
            ->where('status', 'success')
            ->latest('checked_in_at')
            ->take(15)
            ->get();

        $todayCount = SelfAttendanceLog::where('attendance_date', today())
            ->where('status', 'success')
            ->count();

        // Ambil sample siswa untuk fitur simulasi uji coba scan
        $students = Student::with('class')->where('status', true)->orderBy('name')->take(60)->get();

        return view('attendance.qr-scanner', compact('classes', 'recentLogs', 'todayCount', 'students'));
    }

    /**
     * Proses pemindaian QR Siswa oleh Guru / Admin
     */
    public function processScanStudentQr(Request $request): JsonResponse
    {
        $this->authorizeStaff();

        $request->validate([
            'qr_code' => 'required|string',
            'class_id' => 'nullable|exists:classes,id',
            'session' => 'nullable|string',
        ]);

        $rawCode = trim($request->input('qr_code'));
        $student = null;

        // 1. Resolve student dari payload QR (format LMS-2025001 atau SIABSEN-2025001)
        if (str_starts_with($rawCode, 'LMS-') || str_starts_with($rawCode, 'SIABSEN-')) {
            $nis = str_replace(['LMS-', 'SIABSEN-'], '', $rawCode);
            $student = Student::with('class')->where('nis', $nis)->first();
        }

        // Cek langsung berdasarkan NIS
        if (!$student) {
            $student = Student::with('class')->where('nis', $rawCode)->first();
        }

        // Cek via helper QrAttendanceService
        if (!$student) {
            $student = app(QrAttendanceService::class)->resolveStudentFromQr($rawCode);
        }

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak dikenali atau data siswa tidak ditemukan (Kode: ' . $rawCode . ').',
            ], 404);
        }

        // Verifikasi filter kelas jika guru memilih kelas tertentu
        $selectedClassId = $request->input('class_id');
        if ($selectedClassId && (int) $student->class_id !== (int) $selectedClassId) {
            $filterClass = Classes::find($selectedClassId);
            return response()->json([
                'success' => false,
                'message' => "Siswa {$student->name} berasal dari kelas {$student->class?->name}, bukan kelas {$filterClass?->name}.",
            ], 422);
        }

        $date = today()->toDateString();
        $session = $request->input('session') ?: ($student->class?->session ?? 'pagi');
        $currentUser = auth()->user();
        $teacher = $currentUser->teacher;

        // 2. Buat / update record Attendance
        $attendance = Attendance::firstOrCreate(
            [
                'date' => $date,
                'class_id' => $student->class_id,
                'session' => $session,
            ],
            [
                'teacher_id' => $teacher?->id ?? ($student->class?->homeroom_teacher_id ?? 1),
            ]
        );

        $detail = AttendanceDetail::firstOrNew([
            'attendance_id' => $attendance->id,
            'student_id' => $student->id,
        ]);

        $wasAlreadyPresent = $detail->exists && $detail->status === 'hadir';

        $detail->status = 'hadir';
        $detail->check_in = $detail->check_in ?: now()->format('H:i:s');
        $detail->notes = 'Hadir via Scan QR oleh ' . ($currentUser->name ?? 'Guru');
        $detail->save();

        // 3. Catat ke SelfAttendanceLog
        $log = SelfAttendanceLog::firstOrNew([
            'student_id' => $student->id,
            'attendance_date' => $date,
        ]);

        $log->user_id = $currentUser->id;
        $log->attendance_detail_id = $detail->id;
        $log->check_in_mode = 'qr_scan_by_teacher';
        $log->status = 'success';
        $log->notes = 'Discan oleh ' . ($currentUser->name ?? 'Staff') . ' (' . ($currentUser->isGuru() ? 'Guru' : 'Admin') . ')';
        if (!$log->exists || !$log->checked_in_at) {
            $log->checked_in_at = now();
        }
        $log->device_type = $request->input('device_type', 'Scanner Kamera Guru/Admin');
        $log->browser = $request->input('browser', 'Browser');
        $log->ip_address = $request->ip();
        $log->save();

        return response()->json([
            'success' => true,
            'already_checked_in' => $wasAlreadyPresent,
            'message' => $wasAlreadyPresent
                ? "{$student->name} sudah tercatat Hadir sebelumnya."
                : "Presensi berhasil! {$student->name} tercatat Hadir.",
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'nis' => $student->nis,
                'class_name' => $student->class?->name ?? 'Tanpa Kelas',
                'photo_url' => $student->photo_url,
                'check_in_time' => now()->format('H:i:s') . ' WIB',
            ],
            'log' => [
                'id' => $log->id,
                'time' => $log->checked_in_at?->format('H:i:s') ?? now()->format('H:i:s'),
                'notes' => $log->notes,
            ],
        ]);
    }

    /**
     * Tampilan Log Presensi QR untuk Staff
     */
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
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isSuperAdmin() || $user->isKepalaSekolah() || $user->isGuru()), 403, 'Akses terbatas untuk Guru dan Admin.');
    }
}

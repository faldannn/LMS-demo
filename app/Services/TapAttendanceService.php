<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\Setting;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TapAttendanceService
{
    /**
     * Get the active API Key for Tap Attendance integration.
     */
    public function getApiKey(): string
    {
        $key = Setting::get('tap_attendance_api_key');
        if (empty($key)) {
            // Also check legacy/kartu_api_key or generate a secure default
            $key = Setting::get('kartu_api_key');
            if (empty($key)) {
                $key = 'tap_nexus_' . bin2hex(random_bytes(8));
                Setting::set('tap_attendance_api_key', $key, 'tap');
            } else {
                Setting::set('tap_attendance_api_key', $key, 'tap');
            }
        }

        return $key;
    }

    /**
     * Validate incoming request API key / token.
     */
    public function validateApiKey(?string $providedKey): bool
    {
        if (empty($providedKey)) {
            return false;
        }

        $expected = $this->getApiKey();

        return hash_equals(trim($expected), trim($providedKey));
    }

    /**
     * Find student by RFID UID, NISN, or NIS.
     */
    public function findStudent(string $identifier): ?Student
    {
        $clean = trim($identifier);
        if ($clean === '') {
            return null;
        }

        return Student::with(['class.homeroomTeacher'])
            ->where(function ($q) use ($clean) {
                $q->where('rfid', $clean)
                  ->orWhere('nisn', $clean)
                  ->orWhere('nis', $clean);
            })
            ->first();
    }

    /**
     * Record student attendance from tap machine.
     *
     * @param string $identifier RFID UID / NISN / NIS
     * @param string|null $deviceId Identifier of the tap machine / gate
     * @param string|null $rawTime Optional timestamp from machine
     * @param string|null $customNotes
     * @return array
     */
    public function recordTap(
        string $identifier,
        ?string $deviceId = 'GATE-TAP-01',
        ?string $rawTime = null,
        ?string $customNotes = null
    ): array {
        $student = $this->findStudent($identifier);

        if (!$student) {
            Log::warning("Tap Attendance: Card '{$identifier}' not found.");
            return [
                'success' => false,
                'action' => 'not_found',
                'message' => "Kartu/Nomor ID '{$identifier}' tidak terdaftar di sistem LMS.",
                'card_id' => $identifier,
                'device_id' => $deviceId,
                'code' => 404,
            ];
        }

        if (!$student->status) {
            Log::warning("Tap Attendance: Student {$student->name} (ID: {$student->id}) is inactive.");
            return [
                'success' => false,
                'action' => 'inactive',
                'message' => "Siswa {$student->name} ({$student->nis}) berstatus tidak aktif.",
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'nis' => $student->nis,
                    'nisn' => $student->nisn,
                ],
                'code' => 403,
            ];
        }

        $cTime = $rawTime ? Carbon::parse($rawTime) : now();
        $date = $cTime->toDateString();
        $session = $student->class?->session ?? 'pagi';
        $teacherId = $student->class?->homeroom_teacher_id ?? 1;

        // Ensure Attendance master record exists
        $attendance = Attendance::firstOrCreate(
            [
                'date' => $date,
                'class_id' => $student->class_id,
                'session' => $session,
            ],
            ['teacher_id' => $teacherId]
        );

        // Check if student has already checked in for this session today
        $existing = AttendanceDetail::where('attendance_id', $attendance->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existing) {
            $formattedTime = $existing->check_in ? Carbon::parse($existing->check_in)->format('H:i') : '-';
            return [
                'success' => true,
                'action' => 'already_present',
                'message' => "{$student->name} sudah tercatat presensi sebelumnya pada pukul {$formattedTime} WIB.",
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'nis' => $student->nis,
                    'nisn' => $student->nisn,
                    'rfid' => $student->rfid,
                    'class' => $student->class?->name ?? 'Tanpa Kelas',
                    'gender' => $student->gender,
                    'photo_url' => $student->photo ? asset('storage/' . $student->photo) : null,
                ],
                'attendance' => [
                    'date' => $attendance->date->toDateString(),
                    'session' => ucfirst($session),
                    'status' => $existing->status,
                    'status_label' => $existing->status_label,
                    'check_in' => $formattedTime,
                    'notes' => $existing->notes,
                ],
                'device_id' => $deviceId,
                'code' => 200,
            ];
        }

        // Determine lateness
        $lateLimit = $session === 'siang'
            ? config('attendance.late_limit_siang', '13:00:00')
            : config('attendance.late_limit', '07:15:00');

        $settingLate = Setting::get('late_threshold');
        if ($settingLate) {
            $lateLimit = strlen($settingLate) === 5 ? "{$settingLate}:00" : $settingLate;
        }

        $timeStr = $cTime->format('H:i:s');
        $status = ($timeStr > $lateLimit) ? 'terlambat' : 'hadir';

        $notes = $customNotes ?: "Presensi Tap Kartu [Mesin: {$deviceId}]";

        $detail = AttendanceDetail::create([
            'attendance_id' => $attendance->id,
            'student_id' => $student->id,
            'status' => $status,
            'check_in' => $timeStr,
            'notes' => $notes,
        ]);

        Log::info("Tap Attendance Recorded: Student {$student->name} status {$status} at {$timeStr} (Device: {$deviceId})");

        return [
            'success' => true,
            'action' => 'recorded',
            'status' => $status,
            'message' => "Presensi berhasil dicatat: {$student->name} (" . ucfirst($status) . ")",
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'rfid' => $student->rfid,
                'class' => $student->class?->name ?? 'Tanpa Kelas',
                'gender' => $student->gender,
                'photo_url' => $student->photo ? asset('storage/' . $student->photo) : null,
            ],
            'attendance' => [
                'date' => $attendance->date->toDateString(),
                'session' => ucfirst($session),
                'status' => $status,
                'status_label' => $detail->status_label,
                'check_in' => $cTime->format('H:i'),
                'notes' => $notes,
            ],
            'device_id' => $deviceId,
            'timestamp' => $cTime->toDateTimeString(),
            'code' => 200,
        ];
    }

    /**
     * Get recent tap records for today.
     */
    public function getRecentTaps(int $limit = 20): array
    {
        $today = today()->toDateString();

        $details = AttendanceDetail::with(['student.class'])
            ->whereHas('attendance', fn ($q) => $q->where('date', $today))
            ->where(function ($q) {
                $q->where('notes', 'like', '%Tap%')
                  ->orWhere('notes', 'like', '%Kartu%')
                  ->orWhere('notes', 'like', '%GATE%')
                  ->orWhereNotNull('check_in');
            })
            ->latest('id')
            ->limit($limit)
            ->get();

        return $details->map(function ($detail) {
            $student = $detail->student;
            return [
                'id' => $detail->id,
                'student_id' => $student?->id,
                'name' => $student?->name ?? 'Siswa',
                'nis' => $student?->nis ?? '-',
                'class' => $student?->class?->name ?? 'Kelas',
                'status' => $detail->status,
                'status_label' => $detail->status_label,
                'check_in' => $detail->check_in ? substr($detail->check_in, 0, 5) . ' WIB' : '-',
                'notes' => $detail->notes,
                'photo_url' => $student?->photo_url,
            ];
        })->toArray();
    }
}

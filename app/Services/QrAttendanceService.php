<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\Classes;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;

class QrAttendanceService
{
    /**
     * Resolve student from encrypted or legacy QR payload.
     */
    public function resolveStudentFromQr(string $payload): ?Student
    {
        try {
            $data = json_decode(Crypt::decryptString($payload), true);
            $studentId = $data['id'] ?? null;

            return $studentId ? Student::with('class')->find($studentId) : null;
        } catch (\Illuminate\Contracts\Encryption\DecryptException) {
            if (str_starts_with($payload, 'LMS-') || str_starts_with($payload, 'SIABSEN-')) {
                $nis = str_replace(['LMS-', 'SIABSEN-'], '', $payload);

                return Student::with('class')->where('nis', $nis)->first();
            }

            return null;
        }
    }

    /**
     * Record attendance for a student (card scan or class QR scan).
     *
     * @return array{success: bool, message: string}
     */
    public function recordAttendance(
        Student $student,
        ?int $teacherId = null,
        ?string $date = null,
        ?int $expectedClassId = null,
    ): array {
        $date ??= today()->toDateString();

        if ($expectedClassId !== null && (int) $student->class_id !== (int) $expectedClassId) {
            return ['success' => false, 'message' => 'Ini bukan QR Code untuk kelas Anda.'];
        }

        if ($date !== today()->toDateString()) {
            return ['success' => false, 'message' => 'QR Code ini sudah kedaluwarsa.'];
        }

        $teacherId ??= $student->class?->homeroom_teacher_id ?? 1;

        $attendance = Attendance::firstOrCreate(
            [
                'date' => $date,
                'class_id' => $student->class_id,
                'session' => $student->class?->session ?? 'pagi',
            ],
            ['teacher_id' => $teacherId]
        );

        $existing = AttendanceDetail::where('attendance_id', $attendance->id)
            ->where('student_id', $student->id)
            ->exists();

        if ($existing) {
            $message = $expectedClassId !== null
                ? 'Anda sudah melakukan absen hari ini.'
                : "{$student->name} sudah tercatat hadir.";

            return ['success' => false, 'message' => $message];
        }

        $status = $this->determineStatus($student);

        AttendanceDetail::create([
            'attendance_id' => $attendance->id,
            'student_id' => $student->id,
            'status' => $status,
            'check_in' => now()->format('H:i:s'),
        ]);

        $message = $expectedClassId !== null
            ? 'Berhasil! Kehadiran dicatat sebagai ' . strtoupper($status)
            : "Berhasil: {$student->name} (" . strtoupper($status) . ')';

        return ['success' => true, 'message' => $message];
    }

    /**
     * Generate encrypted class QR payload for the given class.
     */
    public function generateClassQrString(int $classId, int $teacherId): string
    {
        $payload = json_encode([
            'class_id' => $classId,
            'date' => today()->toDateString(),
            'teacher_id' => $teacherId,
            'ts' => time(),
        ]);

        return Crypt::encryptString($payload);
    }

    /**
     * Parse decrypted class QR payload.
     *
     * @return array{class_id: int|null, date: string|null, teacher_id: int}
     */
    public function parseClassQrPayload(string $payload): array
    {
        $data = json_decode(Crypt::decryptString($payload), true);

        return [
            'class_id' => $data['class_id'] ?? null,
            'date' => $data['date'] ?? null,
            'teacher_id' => $data['teacher_id'] ?? 1,
        ];
    }

    /**
     * Classes available for QR generation based on user role.
     */
    public function getClassesForQrGeneration($user): Collection
    {
        if ($user->isGuru()) {
            $classes = Classes::where('homeroom_teacher_id', $user->teacher?->id)
                ->orderBy('name')
                ->get(['id', 'name', 'grade']);

            if ($classes->isNotEmpty()) {
                return $classes;
            }
        }

        return Classes::orderBy('name')->get(['id', 'name', 'grade']);
    }

    /**
     * Live attendance data for QR monitor polling.
     */
    public function getLiveAttendanceData(int $classId): Collection
    {
        $attendance = Attendance::where('date', today()->toDateString())
            ->where('class_id', $classId)
            ->first();

        if (!$attendance) {
            return collect();
        }

        return AttendanceDetail::with('student:id,name,nis')
            ->where('attendance_id', $attendance->id)
            ->orderByDesc('id')
            ->get()
            ->map(fn ($detail) => [
                'name' => $detail->student->name,
                'nis' => $detail->student->nis,
                'time' => $detail->check_in,
                'status' => $detail->status,
            ]);
    }

    private function determineStatus(?Student $student = null): string
    {
        $session = $student?->class?->session ?? 'pagi';
        $lateLimit = $session === 'siang'
            ? config('attendance.late_limit_siang', '13:00:00')
            : config('attendance.late_limit', '07:15:00');

        return now()->format('H:i:s') > $lateLimit ? 'terlambat' : 'hadir';
    }
}

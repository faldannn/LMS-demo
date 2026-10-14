<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\SelfAttendanceLog;
use App\Models\Student;
use App\Models\StudentFaceProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class SelfAttendanceService
{
    public function __construct(
        private readonly GeofenceService $geofence,
        private readonly FileStorageService $storage,
        private readonly QrAttendanceService $qrAttendance,
    ) {
    }

    public function getOrCreateFaceProfile(Student $student): StudentFaceProfile
    {
        return StudentFaceProfile::firstOrCreate(
            ['student_id' => $student->id],
            ['reference_photo_path' => $student->photo]
        );
    }

    public function saveFaceDescriptor(Student $student, array $descriptor): StudentFaceProfile
    {
        $profile = $this->getOrCreateFaceProfile($student);
        $profile->update(['landmark_descriptor' => $descriptor]);

        return $profile->fresh();
    }

    public function processCheckIn(Student $student, array $payload, ?UploadedFile $photo, array $meta): array
    {
        $date = today()->toDateString();
        $mode = $payload['check_in_mode'] ?? 'full';

        if (SelfAttendanceLog::where('student_id', $student->id)->where('attendance_date', $date)->where('status', 'success')->exists()) {
            return ['success' => false, 'message' => 'Anda sudah presensi hari ini.', 'code' => 'duplicate'];
        }

        $session = $student->class?->session ?? 'pagi';
        $checkinStartStr = $session === 'siang'
            ? config('attendance.student_checkin_start_siang', '11:30:00')
            : config('attendance.student_checkin_start', '06:00:00');

        $now = now();
        $checkinStartTime = \Illuminate\Support\Carbon::createFromTimeString($checkinStartStr);
        if ($now->lessThan($checkinStartTime)) {
            return [
                'success' => false,
                'message' => 'Presensi belum dibuka. Presensi ' . ($session === 'siang' ? 'siang' : 'pagi') . ' dibuka mulai pukul ' . substr($checkinStartStr, 0, 5) . ' WIB.',
                'code' => 'too_early',
            ];
        }

        $qrCode = $payload['qr_code'] ?? null;
        $qrNote = 'Scan QR Kelas ' . ($student->class?->name ?? '');

        if (!empty($qrCode)) {
            try {
                $classPayload = $this->qrAttendance->parseClassQrPayload($qrCode);
                if (!empty($classPayload['class_id']) && (int) $classPayload['class_id'] !== (int) $student->class_id) {
                    return ['success' => false, 'message' => 'QR Code ini bukan untuk kelas Anda.', 'code' => 'invalid_class_qr'];
                }
                $qrNote = 'Scan QR Kelas: ' . ($student->class?->name ?? $classPayload['class_id']);
            } catch (\Exception $e) {
                $qrNote = 'Scan QR: ' . (strlen($qrCode) > 30 ? substr($qrCode, 0, 30) . '...' : $qrCode);
            }
        }

        return DB::transaction(function () use ($student, $payload, $photo, $meta, $date, $mode, $qrNote) {
            $photoPath = null;
            if ($photo) {
                $photoPath = $this->storage->upload($photo, 'attendance/self/' . $student->id)['file_path'];
            }

            $attendanceResult = $this->qrAttendance->recordAttendance($student);
            $detail = null;
            if ($attendanceResult['success']) {
                $detail = AttendanceDetail::whereHas('attendance', fn ($q) => $q->where('date', $date))
                    ->where('student_id', $student->id)
                    ->latest('id')
                    ->first();
            } elseif ($attendanceResult['message'] !== 'Anda sudah melakukan absen hari ini.') {
                return ['success' => false, 'message' => $attendanceResult['message'], 'code' => 'attendance_failed'];
            }

            $log = SelfAttendanceLog::create([
                'student_id' => $student->id,
                'user_id' => $meta['user_id'] ?? null,
                'attendance_detail_id' => $detail?->id,
                'attendance_date' => $date,
                'latitude' => null,
                'longitude' => null,
                'distance_meters' => null,
                'within_radius' => false,
                'face_detection_confidence' => null,
                'face_match_confidence' => null,
                'face_matched' => false,
                'photo_path' => $photoPath,
                'device_type' => $meta['device_type'] ?? 'Desktop',
                'browser' => $meta['browser'] ?? 'Browser',
                'ip_address' => $meta['ip_address'] ?? '127.0.0.1',
                'user_agent' => $meta['user_agent'] ?? null,
                'check_in_mode' => $mode ?: 'qr_scan',
                'status' => 'success',
                'fallback_reason' => $payload['fallback_reason'] ?? null,
                'notes' => $payload['notes'] ?? $qrNote,
                'checked_in_at' => now(),
            ]);

            $statusText = strtoupper($detail?->status ?? 'HADIR');

            return [
                'success' => true,
                'message' => 'Presensi QR berhasil dicatat. Status: ' . $statusText,
                'status' => $detail?->status ?? 'hadir',
                'log' => $log,
                'distance_meters' => null,
                'within_radius' => false,
                'face_matched' => false,
                'checked_in_at' => $log->checked_in_at->format('H:i:s') . ' WIB',
            ];
        });
    }

    private function resolveGeofence(array $payload, string $mode): array
    {
        return ['valid' => true, 'within_radius' => false, 'distance_meters' => null];
    }

    private function resolveFaceValidation(array $payload, string $mode): array
    {
        return ['valid' => true, 'message' => 'OK'];
    }
}

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

        $geo = $this->resolveGeofence($payload, $mode);
        $face = $this->resolveFaceValidation($payload, $mode);

        if (!$face['valid']) {
            return ['success' => false, 'message' => $face['message'], 'code' => 'face_failed'];
        }

        if (!$geo['valid']) {
            return ['success' => false, 'message' => $geo['message'], 'code' => 'gps_failed'];
        }

        return DB::transaction(function () use ($student, $payload, $photo, $meta, $date, $mode, $geo, $face) {
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
                'latitude' => $payload['latitude'] ?? null,
                'longitude' => $payload['longitude'] ?? null,
                'distance_meters' => $geo['distance_meters'],
                'within_radius' => $geo['within_radius'],
                'face_detection_confidence' => $payload['face_detection_confidence'] ?? null,
                'face_match_confidence' => $payload['face_match_confidence'] ?? null,
                'face_matched' => (bool) ($payload['face_matched'] ?? false),
                'photo_path' => $photoPath,
                'device_type' => $meta['device_type'] ?? null,
                'browser' => $meta['browser'] ?? null,
                'ip_address' => $meta['ip_address'] ?? null,
                'user_agent' => $meta['user_agent'] ?? null,
                'check_in_mode' => $mode,
                'status' => 'success',
                'fallback_reason' => $payload['fallback_reason'] ?? null,
                'notes' => $payload['notes'] ?? null,
                'checked_in_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Presensi berhasil dicatat.',
                'log' => $log,
                'distance_meters' => $geo['distance_meters'],
                'within_radius' => $geo['within_radius'],
                'face_matched' => $log->face_matched,
                'checked_in_at' => $log->checked_in_at->toIso8601String(),
            ];
        });
    }

    private function resolveGeofence(array $payload, string $mode): array
    {
        if (in_array($mode, ['camera_fallback', 'manual_fallback'], true)) {
            if (!config('attendance.self_checkin.allow_gps_fallback') && $mode !== 'manual_fallback') {
                return ['valid' => false, 'message' => 'GPS tidak tersedia dan fallback tidak diizinkan.', 'distance_meters' => null, 'within_radius' => false];
            }

            if ($mode === 'manual_fallback') {
                return ['valid' => (bool) config('attendance.self_checkin.allow_manual_fallback'), 'message' => 'Fallback manual tidak diizinkan.', 'distance_meters' => null, 'within_radius' => false];
            }

            return ['valid' => true, 'distance_meters' => null, 'within_radius' => false];
        }

        if (!isset($payload['latitude'], $payload['longitude'])) {
            return ['valid' => false, 'message' => 'Koordinat GPS wajib diisi.', 'distance_meters' => null, 'within_radius' => false];
        }

        $result = $this->geofence->isWithinRadius((float) $payload['latitude'], (float) $payload['longitude']);

        if (!$result['within_radius'] && $mode === 'gps_fallback') {
            return array_merge(['valid' => (bool) config('attendance.self_checkin.allow_gps_fallback'), 'message' => 'Anda berada di luar radius sekolah.'], $result);
        }

        if (!$result['within_radius']) {
            return array_merge(['valid' => false, 'message' => 'Anda berada di luar radius sekolah (' . round($result['distance_meters']) . ' m).'], $result);
        }

        return array_merge(['valid' => true], $result);
    }

    private function resolveFaceValidation(array $payload, string $mode): array
    {
        return ['valid' => true, 'message' => 'OK'];
    }
}

<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;

class TeacherAttendanceService
{
    public function __construct(
        private readonly GeofenceService $geofence,
        private readonly FileStorageService $storage,
    ) {
    }

    public function getTodayAttendance(Teacher $teacher): ?TeacherAttendance
    {
        return TeacherAttendance::where('teacher_id', $teacher->id)
            ->where('date', today()->toDateString())
            ->first();
    }

    public function processCheckIn(Teacher $teacher, array $payload, ?UploadedFile $photo, array $meta): array
    {
        $today = today()->toDateString();
        $existing = TeacherAttendance::where('teacher_id', $teacher->id)
            ->where('date', $today)
            ->first();

        if ($existing && $existing->check_in_at) {
            return [
                'success' => false,
                'message' => 'Anda sudah melakukan Absen Masuk hari ini pada pukul ' . $existing->check_in_at->format('H:i:s'),
                'code' => 'already_checked_in',
            ];
        }

        // Validate time vs teacher_checkin_start
        $checkinStartStr = config('attendance.teacher_checkin_start', '06:00:00');
        $now = now();
        $checkinStartTime = Carbon::createFromTimeString($checkinStartStr);

        if ($now->lessThan($checkinStartTime)) {
            return [
                'success' => false,
                'message' => 'Absen Masuk belum dibuka. Absen masuk dimulai pukul ' . substr($checkinStartStr, 0, 5) . ' WIB.',
                'code' => 'too_early',
            ];
        }

        // Validate geofence
        $geo = $this->resolveGeofence($payload);
        if (!$geo['valid']) {
            return [
                'success' => false,
                'message' => $geo['message'],
                'code' => 'gps_failed',
            ];
        }

        // Upload photo
        $photoPath = null;
        if ($photo) {
            $photoPath = $this->storage->upload($photo, 'attendance/teacher/' . $teacher->id)['file_path'];
        }

        // Determine status (hadir vs terlambat) based on teacher_late_limit config
        $lateLimitStr = config('attendance.teacher_late_limit', '07:15:00');
        $lateLimitTime = Carbon::createFromTimeString($lateLimitStr);
        $status = $now->greaterThan($lateLimitTime) ? 'terlambat' : 'hadir';

        $attendance = TeacherAttendance::updateOrCreate(
            [
                'teacher_id' => $teacher->id,
                'date' => $today,
            ],
            [
                'user_id' => $meta['user_id'] ?? null,
                'status' => $status,
                'check_in_at' => $now,
                'check_in_photo' => $photoPath,
                'check_in_lat' => $payload['latitude'] ?? null,
                'check_in_lng' => $payload['longitude'] ?? null,
                'check_in_distance_meters' => $geo['distance_meters'],
                'check_in_within_radius' => $geo['within_radius'],
                'check_in_mode' => $payload['check_in_mode'] ?? 'full',
            ]
        );

        return [
            'success' => true,
            'message' => 'Absen Masuk berhasil dicatat (' . ucfirst($status) . ') pada pukul ' . $now->format('H:i:s'),
            'attendance' => $attendance,
        ];
    }

    public function processCheckOut(Teacher $teacher, array $payload, ?UploadedFile $photo, array $meta): array
    {
        $today = today()->toDateString();
        $existing = TeacherAttendance::where('teacher_id', $teacher->id)
            ->where('date', $today)
            ->first();

        if (!$existing || !$existing->check_in_at) {
            return [
                'success' => false,
                'message' => 'Anda belum melakukan Absen Masuk hari ini.',
                'code' => 'no_check_in',
            ];
        }

        if ($existing->check_out_at) {
            return [
                'success' => false,
                'message' => 'Anda sudah melakukan Absen Pulang hari ini pada pukul ' . $existing->check_out_at->format('H:i:s'),
                'code' => 'already_checked_out',
            ];
        }

        // Validate time vs teacher_checkout_start & teacher_checkout_end (cutoff)
        $checkoutStartStr = config('attendance.teacher_checkout_start', '15:00:00');
        $checkoutEndStr = config('attendance.teacher_checkout_end', '18:00:00');
        $now = now();
        $checkoutStartTime = Carbon::createFromTimeString($checkoutStartStr);
        $checkoutEndTime = Carbon::createFromTimeString($checkoutEndStr);

        if ($now->lessThan($checkoutStartTime)) {
            return [
                'success' => false,
                'message' => 'Absen Pulang belum dibuka. Absen pulang dimulai pukul ' . substr($checkoutStartStr, 0, 5) . ' WIB.',
                'code' => 'too_early',
            ];
        }

        if ($now->greaterThan($checkoutEndTime)) {
            return [
                'success' => false,
                'message' => 'Waktu Absen Pulang telah berakhir (Batas: ' . substr($checkoutEndStr, 0, 5) . ' WIB). Anda sudah tidak dapat melakukan presensi pulang hari ini.',
                'code' => 'checkout_expired',
            ];
        }

        // Validate geofence
        $geo = $this->resolveGeofence($payload);
        if (!$geo['valid']) {
            return [
                'success' => false,
                'message' => $geo['message'],
                'code' => 'gps_failed',
            ];
        }

        // Upload photo
        $photoPath = null;
        if ($photo) {
            $photoPath = $this->storage->upload($photo, 'attendance/teacher/' . $teacher->id)['file_path'];
        }

        $existing->update([
            'check_out_at' => $now,
            'check_out_photo' => $photoPath,
            'check_out_lat' => $payload['latitude'] ?? null,
            'check_out_lng' => $payload['longitude'] ?? null,
            'check_out_distance_meters' => $geo['distance_meters'],
            'check_out_within_radius' => $geo['within_radius'],
            'check_out_mode' => $payload['check_out_mode'] ?? 'full',
        ]);

        return [
            'success' => true,
            'message' => 'Absen Pulang berhasil dicatat pada pukul ' . $now->format('H:i:s'),
            'attendance' => $existing->fresh(),
        ];
    }

    private function resolveGeofence(array $payload): array
    {
        $lat = isset($payload['latitude']) ? (float) $payload['latitude'] : null;
        $lng = isset($payload['longitude']) ? (float) $payload['longitude'] : null;

        if ($lat === null || $lng === null) {
            return [
                'valid' => false,
                'message' => 'Lokasi GPS tidak ditemukan. Harap aktifkan GPS dan izinkan lokasi pada browser.',
                'distance_meters' => null,
                'within_radius' => false,
            ];
        }

        $calc = $this->geofence->isWithinRadius($lat, $lng);

        return [
            'valid' => $calc['within_radius'],
            'message' => $calc['within_radius'] ? 'Lokasi valid.' : 'Anda berada di luar radius lokasi sekolah (' . round($calc['distance_meters']) . ' m).',
            'distance_meters' => $calc['distance_meters'],
            'within_radius' => $calc['within_radius'],
        ];
    }
}

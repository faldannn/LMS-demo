<?php

return [
    // Presensi Siswa Pagi
    'student_checkin_start' => env('ATTENDANCE_STUDENT_CHECKIN_START', '06:00'),
    'late_limit' => env('ATTENDANCE_LATE_LIMIT', '07:15'),
    'student_checkout_start' => env('ATTENDANCE_STUDENT_CHECKOUT_START', '14:00'),
    'student_checkout_end' => env('ATTENDANCE_STUDENT_CHECKOUT_END', '17:00'),

    // Presensi Siswa Siang
    'student_checkin_start_siang' => env('ATTENDANCE_STUDENT_CHECKIN_START_SIANG', '11:30'),
    'late_limit_siang' => env('ATTENDANCE_LATE_LIMIT_SIANG', '13:00'),
    'student_checkout_start_siang' => env('ATTENDANCE_STUDENT_CHECKOUT_START_SIANG', '17:30'),
    'student_checkout_end_siang' => env('ATTENDANCE_STUDENT_CHECKOUT_END_SIANG', '20:00'),

    // Presensi Guru
    'teacher_checkin_start' => env('ATTENDANCE_TEACHER_CHECKIN_START', '06:00'),
    'teacher_late_limit' => env('ATTENDANCE_TEACHER_LATE_LIMIT', '07:15'),
    'teacher_checkout_start' => env('ATTENDANCE_TEACHER_CHECKOUT_START', '15:00'),
    'teacher_checkout_end' => env('ATTENDANCE_TEACHER_CHECKOUT_END', '18:00'),

    'start_time' => env('ATTENDANCE_START_TIME', '07:00'),
    'end_time' => env('ATTENDANCE_END_TIME', '15:30'),
    'min_percentage' => (int) env('ATTENDANCE_MIN_PERCENTAGE', 75),
    'working_days' => (int) env('ATTENDANCE_WORKING_DAYS', 22),
    'statuses' => [
        'hadir' => 'Hadir',
        'sakit' => 'Sakit',
        'izin' => 'Izin',
        'alfa' => 'Tidak Hadir (Alfa)',
        'terlambat' => 'Terlambat',
    ],

    /*
    |--------------------------------------------------------------------------
    | Geofence Presensi Mandiri (GPS + Haversine)
    |--------------------------------------------------------------------------
    */
    'geofence' => [
        'latitude' => (float) env('SCHOOL_LATITUDE', -6.2349),
        'longitude' => (float) env('SCHOOL_LONGITUDE', 106.9896),
        'radius_meters' => (int) env('SCHOOL_RADIUS_METERS', 150),
    ],

    'face' => [
        'detection_min_confidence' => (float) env('FACE_DETECTION_MIN', 0.65),
        'match_min_confidence' => (float) env('FACE_MATCH_MIN', 0.72),
    ],

    'self_checkin' => [
        'allow_camera_fallback' => env('ATTENDANCE_ALLOW_CAMERA_FALLBACK', true),
        'allow_gps_fallback' => env('ATTENDANCE_ALLOW_GPS_FALLBACK', true),
        'allow_manual_fallback' => env('ATTENDANCE_ALLOW_MANUAL_FALLBACK', true),
    ],
];
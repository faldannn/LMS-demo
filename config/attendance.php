<?php

return [
    'start_time' => env('ATTENDANCE_START_TIME', '07:00'),
    'late_limit' => env('ATTENDANCE_LATE_LIMIT', '07:15'),
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
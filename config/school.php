<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Identitas Sekolah & Sistem
    |--------------------------------------------------------------------------
    */
    'system_name' => env('SYSTEM_NAME', 'LMS SMA Islam Assyafiiyah 02 Bekasi'),
    'system_short' => env('SYSTEM_SHORT', 'LMS Assyafiiyah'),
    'system_version' => env('SYSTEM_VERSION', '4.0'),

    'name' => env('SCHOOL_NAME', 'SMA Islam Assyafiiyah 02 Bekasi'),
    'npsn' => env('SCHOOL_NPSN', ''),
    'address' => env('SCHOOL_ADDRESS', 'Jl. Raya Bekasi, Bekasi, Jawa Barat'),
    'phone' => env('SCHOOL_PHONE', ''),
    'email' => env('SCHOOL_EMAIL', 'info@assyafiiyah02bekasi.sch.id'),
    'principal' => env('SCHOOL_PRINCIPAL', 'Kepala Sekolah'),
    'type' => env('SCHOOL_TYPE', 'SMA Islam'),

    /*
    |--------------------------------------------------------------------------
    | Branding Visual
    |--------------------------------------------------------------------------
    */
    'brand' => [
        'primary' => env('BRAND_PRIMARY', '#15803d'),
        'primary_dark' => env('BRAND_PRIMARY_DARK', '#166534'),
        'primary_light' => env('BRAND_PRIMARY_LIGHT', '#dcfce7'),
        'logo_letter' => env('BRAND_LOGO_LETTER', 'A'),
        'favicon' => '/images/logo.png',
        'logo' => '/images/logo.png',
    ],

    'developer' => env('SYSTEM_DEVELOPER', 'NexDigi System'),
];

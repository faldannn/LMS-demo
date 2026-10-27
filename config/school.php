<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Identitas Sekolah & Sistem
    |--------------------------------------------------------------------------
    */
    'system_name' => env('SYSTEM_NAME', 'Sekolah Digital'),
    'system_short' => env('SYSTEM_SHORT', 'Sekolah Digital'),
    'system_version' => env('SYSTEM_VERSION', 'v.1.0'),

    'name' => env('SCHOOL_NAME', 'Sekolah Digital'),
    'npsn' => env('SCHOOL_NPSN', ''),
    'address' => env('SCHOOL_ADDRESS', 'Jl. Raya Bekasi, Bekasi, Jawa Barat'),
    'phone' => env('SCHOOL_PHONE', ''),
    'email' => env('SCHOOL_EMAIL', 'info@sekolahdigital.sch.id'),
    'principal' => env('SCHOOL_PRINCIPAL', 'Kepala Sekolah'),
    'type' => env('SCHOOL_TYPE', 'Sekolah Digital'),

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
        'favicon' => '/images/logo-sekolah.png',
        'logo' => '/images/logo-sekolah.png',
    ],

    'developer' => env('SYSTEM_DEVELOPER', 'NexDigi System'),
];

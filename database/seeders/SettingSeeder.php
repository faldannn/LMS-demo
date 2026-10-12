<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['school_name', 'SMA Islam Assyafiiyah 02 Bekasi', 'school'],
            ['school_npsn', '', 'school'],
            ['school_address', 'Jl. Raya Bekasi, Bekasi, Jawa Barat', 'school'],
            ['school_phone', '', 'school'],
            ['school_email', 'info@assyafiiyah02bekasi.sch.id', 'school'],
            ['school_principal', 'Kepala Sekolah', 'school'],
            ['att_start_time', '07:00', 'attendance'],
            ['att_late_limit', '07:15', 'attendance'],
            ['att_late_limit_siang', '13:00', 'attendance'],
            ['att_teacher_late_limit', '07:15:00', 'attendance'],
            ['att_teacher_checkout_start', '15:00:00', 'attendance'],
            ['att_end_time', '15:30', 'attendance'],
            ['att_min_pct', '75', 'attendance'],
            ['att_working_days', '22', 'attendance'],
            ['feature_subject_attendance', '1', 'features'],
            ['subject_attendance', '1', 'features'],
        ];

        foreach ($settings as [$key, $value, $group]) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group]
            );
        }
    }
}

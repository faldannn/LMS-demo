<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AcademicYearSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            TeacherSeeder::class,
            MajorSeeder::class,
            ClassSeeder::class,
            StudentSeeder::class,
            DummyUserSeeder::class,
            ScheduleSeeder::class,
            AttendanceSeeder::class,
            SettingSeeder::class,
            SubjectSeeder::class,
            ClassSubjectSeeder::class,
        ]);
    }
}
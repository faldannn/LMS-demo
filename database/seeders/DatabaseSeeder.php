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
            SubjectSeeder::class,
            TeacherSeeder::class,
            MajorSeeder::class,
            ClassSeeder::class,
            StudentSeeder::class,
            DummyUserSeeder::class,
            ScheduleSeeder::class,
            AttendanceSeeder::class,
            SettingSeeder::class,
            ClassSubjectSeeder::class,
            CbtDemoSeeder::class,
        ]);
    }
}
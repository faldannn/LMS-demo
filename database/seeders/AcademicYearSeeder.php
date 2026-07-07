<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        $year = AcademicYear::create([
            'year' => '2024/2025',
            'description' => 'Tahun Ajaran 2024/2025',
            'is_active' => true,
        ]);

        Semester::create([
            'academic_year_id' => $year->id,
            'number' => 1,
            'start_date' => '2024-07-15',
            'end_date' => '2024-12-21',
            'is_active' => false,
        ]);

        Semester::create([
            'academic_year_id' => $year->id,
            'number' => 2,
            'start_date' => '2025-01-06',
            'end_date' => '2025-06-14',
            'is_active' => true,
        ]);
    }
}

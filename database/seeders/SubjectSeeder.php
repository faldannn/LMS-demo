<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['code' => 'PAI', 'name' => 'Pendidikan Agama Islam', 'group' => 'Muatan Nasional', 'grade' => null],
            ['code' => 'PPKN', 'name' => 'Pendidikan Pancasila dan Kewarganegaraan', 'group' => 'Muatan Nasional', 'grade' => null],
            ['code' => 'B-IND', 'name' => 'Bahasa Indonesia', 'group' => 'Muatan Nasional', 'grade' => null],
            ['code' => 'MTK-W', 'name' => 'Matematika Wajib', 'group' => 'Muatan Nasional', 'grade' => null],
            ['code' => 'B-ING', 'name' => 'Bahasa Inggris', 'group' => 'Muatan Nasional', 'grade' => null],
            ['code' => 'SEJ', 'name' => 'Sejarah Indonesia', 'group' => 'Muatan Nasional', 'grade' => null],
            ['code' => 'MTK-P', 'name' => 'Matematika Peminatan', 'group' => 'Muatan Peminatan', 'grade' => null],
            ['code' => 'FIS', 'name' => 'Fisika', 'group' => 'Muatan Peminatan', 'grade' => null],
            ['code' => 'KIM', 'name' => 'Kimia', 'group' => 'Muatan Peminatan', 'grade' => null],
            ['code' => 'BIO', 'name' => 'Biologi', 'group' => 'Muatan Peminatan', 'grade' => null],
            ['code' => 'EKO', 'name' => 'Ekonomi', 'group' => 'Muatan Peminatan', 'grade' => null],
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(['code' => $subject['code']], $subject);
        }
    }
}

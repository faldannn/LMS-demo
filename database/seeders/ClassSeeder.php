<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Major;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $teachers = Teacher::orderBy('id')->get();
        $majors = Major::pluck('id', 'code');

        $classes = [
            ['X MIPA 1', 10, 'MIPA', 1, 'R101', 32],
            ['X MIPA 2', 10, 'MIPA', 2, 'R102', 30],
            ['X IPS 1', 10, 'IPS', 4, 'R103', 28],
            ['XI MIPA 1', 11, 'MIPA', 3, 'R201', 33],
            ['XI IPS 1', 11, 'IPS', 4, 'R202', 29],
            ['XII IPA 1', 12, 'MIPA', 1, 'R301', 31],
            ['XII IPS 1', 12, 'IPS', 4, 'R302', 27],
        ];

        foreach ($classes as [$name, $grade, $majorCode, $teacherIdx, $room, $max]) {
            Classes::create([
                'name' => $name,
                'grade' => $grade,
                'major_id' => $majors[$majorCode] ?? null,
                'homeroom_teacher_id' => $teachers[$teacherIdx - 1]?->id,
                'academic_year_id' => $activeYear->id,
                'room' => $room,
                'max_students' => $max,
            ]);
        }
    }
}

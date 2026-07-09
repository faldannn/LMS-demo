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
        $teachers   = Teacher::orderBy('id')->get();
        $majors     = Major::pluck('id', 'code');

        // teacherIdx: 1-based index into $teachers ordered by id, null = no homeroom teacher
        // Rule: each teacher can only appear ONCE as homeroom teacher
        $classes = [
            //  name          grade  major  teacherIdx  room   max
            ['X MIPA 1',   10, 'MIPA', 1,    'R101', 32],  // Siti Rahayu
            ['X MIPA 2',   10, 'MIPA', 2,    'R102', 30],  // Budi Santoso
            ['X IPS 1',    10, 'IPS',  4,    'R103', 28],  // Ahmad Firdaus
            ['XI MIPA 1',  11, 'MIPA', 3,    'R201', 33],  // Indah Permata
            ['XI IPS 1',   11, 'IPS',  null, 'R202', 29],  // No homeroom yet (was duplicate)
            ['XII IPA 1',  12, 'MIPA', null, 'R301', 31],  // No homeroom yet (was duplicate)
            ['XII IPS 1',  12, 'IPS',  null, 'R302', 27],  // No homeroom yet (was duplicate)
        ];

        foreach ($classes as [$name, $grade, $majorCode, $teacherIdx, $room, $max]) {
            Classes::create([
                'name'                => $name,
                'grade'               => $grade,
                'major_id'            => $majors[$majorCode] ?? null,
                'homeroom_teacher_id' => $teacherIdx ? ($teachers[$teacherIdx - 1]?->id) : null,
                'academic_year_id'    => $activeYear->id,
                'room'                => $room,
                'max_students'        => $max,
            ]);
        }
    }
}

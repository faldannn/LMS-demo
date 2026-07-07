<?php

namespace Database\Seeders;

use App\Models\Classes;
use App\Models\ClassSubject;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class ClassSubjectSeeder extends Seeder
{
    public function run(): void
    {
        $classes = Classes::all();
        $subjects = Subject::all();
        $teachers = Teacher::all();
        $academicYear = AcademicYear::where('is_active', true)->first();

        if ($classes->isEmpty() || $subjects->isEmpty() || $teachers->isEmpty() || !$academicYear) {
            return;
        }

        foreach ($classes as $class) {
            // Assign 3 random subjects to each class
            $randomSubjects = $subjects->random(min(3, $subjects->count()));
            
            foreach ($randomSubjects as $subject) {
                $randomTeacher = $teachers->random();
                
                ClassSubject::firstOrCreate([
                    'class_id' => $class->id,
                    'subject_id' => $subject->id,
                    'academic_year_id' => $academicYear->id,
                ], [
                    'teacher_id' => $randomTeacher->id,
                ]);
            }
        }
    }
}

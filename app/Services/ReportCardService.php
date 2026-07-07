<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\ReportCard;
use App\Models\ReportCardDetail;
use App\Models\Student;
use App\Models\ClassSubject;
use Illuminate\Support\Facades\DB;

class ReportCardService
{
    public function generateForClass(Classes $class, AcademicYear $academicYear, string $semester): array
    {
        $students = $class->students()
            ->where('status', true)
            ->with([
                'attendanceDetails',
                'assignmentSubmissions.grade',
                'quizAttempts' => function ($q) {
                    $q->where('status', 'finished');
                },
            ])
            ->get();
            
        $classSubjects = ClassSubject::with(['subject', 'assignments', 'quizzes'])->where('class_id', $class->id)->get();
        
        $generatedCount = 0;
        
        DB::transaction(function () use ($students, $classSubjects, $academicYear, $semester, &$generatedCount) {
            foreach ($students as $student) {
                $details = $student->attendanceDetails;
                $hadir = $details->where('status', 'hadir')->count();
                $izin = $details->where('status', 'izin')->count();
                $sakit = $details->where('status', 'sakit')->count();
                $alfa = $details->where('status', 'alfa')->count();

                $reportCard = ReportCard::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'academic_year_id' => $academicYear->id,
                        'semester' => $semester,
                    ],
                    [
                        'attendance_hadir' => $hadir,
                        'attendance_izin' => $izin,
                        'attendance_sakit' => $sakit,
                        'attendance_alfa' => $alfa,
                    ]
                );
                
                foreach ($classSubjects as $cs) {
                    $assignmentScore = $this->calculateAssignmentScore($student, $cs);
                    $quizScore = $this->calculateQuizScore($student, $cs);
                    
                    // Formula: 40% tugas + 60% kuis
                    $finalScore = ($assignmentScore * 0.4) + ($quizScore * 0.6);
                    $grade = $this->determineGrade($finalScore);
                    
                    ReportCardDetail::updateOrCreate(
                        [
                            'report_card_id' => $reportCard->id,
                            'subject_id' => $cs->subject_id,
                        ],
                        [
                            'assignment_score' => $assignmentScore,
                            'quiz_score' => $quizScore,
                            'final_score' => $finalScore,
                            'grade' => $grade,
                        ]
                    );
                }
                $generatedCount++;
            }
        });
        
        return [
            'success' => true,
            'count' => $generatedCount
        ];
    }
    
    private function calculateAssignmentScore(Student $student, ClassSubject $cs): float
    {
        $assignments = $cs->assignments;
        if ($assignments->isEmpty()) return 0;
        
        $totalMax = $assignments->sum('max_score');
        if ($totalMax == 0) return 0;
        
        $earned = 0;
        foreach ($assignments as $assignment) {
            $submission = $student->assignmentSubmissions->firstWhere('assignment_id', $assignment->id);
            if ($submission && $submission->grade) {
                $earned += $submission->grade->score;
            }
        }
        
        return ($earned / $totalMax) * 100;
    }
    
    private function calculateQuizScore(Student $student, ClassSubject $cs): float
    {
        $quizzes = $cs->quizzes;
        if ($quizzes->isEmpty()) return 0;
        
        $earned = 0;
        foreach ($quizzes as $quiz) {
            $attempt = $student->quizAttempts->firstWhere('quiz_id', $quiz->id);
            if ($attempt) {
                $earned += $attempt->score; // out of 100
            }
        }
        
        return $earned / $quizzes->count(); // average
    }
    
    private function determineGrade(float $score): string
    {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'E';
    }

    /**
     * Average final score per class for dashboard chart.
     */
    public function getClassGradeAverages(string $semester = 'ganjil'): array
    {
        $classAverages = DB::table('classes')
            ->select('classes.name', DB::raw('COALESCE(AVG(report_card_details.final_score), 0) as average_score'))
            ->leftJoin('students', 'classes.id', '=', 'students.class_id')
            ->leftJoin('report_cards', function ($join) use ($semester) {
                $join->on('students.id', '=', 'report_cards.student_id')
                    ->where('report_cards.semester', '=', $semester);
            })
            ->leftJoin('report_card_details', 'report_cards.id', '=', 'report_card_details.report_card_id')
            ->groupBy('classes.id', 'classes.name')
            ->orderBy('classes.name')
            ->get();

        return [
            'labels' => $classAverages->pluck('name')->values(),
            'data' => $classAverages->pluck('average_score')->map(fn ($val) => round((float) $val, 2))->values(),
        ];
    }
}

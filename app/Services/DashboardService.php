<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\Classes;
use App\Models\ClassSubject;
use App\Models\Material;
use App\Models\ReportCard;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function __construct(private readonly AttendanceService $attendanceService)
    {
    }

    public function viewFor(User $user): string
    {
        return match ($user->role) {
            UserRole::Siswa => 'dashboard.siswa',
            UserRole::Guru => 'dashboard.guru',
            default => 'dashboard.kepala-sekolah',
        };
    }

    public function buildFor(User $user): array
    {
        $common = [
            'user' => $user,
            'greeting' => $this->greeting(),
            'today_label' => now()->translatedFormat('l, d F Y'),
        ];

        return match ($user->role) {
            UserRole::Siswa => array_merge($common, $this->buildSiswaDashboard($user)),
            UserRole::Guru => array_merge($common, $this->buildGuruDashboard($user)),
            default => array_merge($common, $this->buildKepalaSekolahDashboard()),
        };
    }

    public function getLmsActivityTrend(int $days = 7): array
    {
        return \Illuminate\Support\Facades\Cache::remember("dashboard_lms_trend_{$days}", 300, function () use ($days) {
            $start = now()->subDays($days - 1)->startOfDay();
            
            $materialsMap = Material::where('created_at', '>=', $start)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')->pluck('count', 'date');
                
            $assignmentsMap = Assignment::where('created_at', '>=', $start)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')->pluck('count', 'date');

            $labels = [];
            $materials = [];
            $assignments = [];
            $quizzes = [];

            for ($i = 0; $i < $days; $i++) {
                $dateObj = $start->copy()->addDays($i);
                $dateStr = $dateObj->toDateString();
                
                $labels[] = $dateObj->translatedFormat('D, d M');
                $materials[] = $materialsMap[$dateStr] ?? 0;
                $assignments[] = $assignmentsMap[$dateStr] ?? 0;
                $quizzes[] = 0;
            }

            return compact('labels', 'materials', 'assignments', 'quizzes');
        });
    }

    private function greeting(): string
    {
        $hour = (int) now()->format('H');

        return match (true) {
            $hour < 11 => 'Selamat pagi',
            $hour < 15 => 'Selamat siang',
            $hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };
    }

    private function buildKepalaSekolahDashboard(): array
    {
        $stats = $this->attendanceService->getTodaySummary();
        $today = today()->toDateString();

        $teachersPresent = Attendance::whereDate('date', $today)
            ->distinct('teacher_id')
            ->count('teacher_id');

        return [
            'totals' => [
                'teachers' => Teacher::active()->count(),
                'students' => Student::active()->count(),
                'classes' => Classes::count(),
                'subjects' => Subject::active()->count(),
            ],
            'attendance_today' => [
                'teachers_present' => $teachersPresent,
                'students_present' => $stats['hadir'] + $stats['terlambat'],
                'students_percentage' => $stats['percentage'],
                'alfa' => $stats['alfa'],
            ],
            'monthly_trend' => $this->attendanceService->getMonthlyTrend(),
            'lms_activity' => $this->getLmsActivityTrend(),
            'announcements' => Announcement::with('creator:id,name')
                ->latest()
                ->limit(6)
                ->get(),
            'class_attendance' => $this->attendanceService->getClassAttendanceSummary($today),
            'low_attendance' => $this->attendanceService->getLowAttendanceStudents(5),
        ];
    }

    private function buildGuruDashboard(User $user): array
    {
        $teacher = $user->teacher;
        $dayOfWeek = (string) now()->format('N');
        $today = today();
        $classSubjectIds = collect();

        $todaySchedules = $teacher
            ? Schedule::with('class:id,name,grade')
                ->where('teacher_id', $teacher->id)
                ->where('day_of_week', $dayOfWeek)
                ->orderBy('start_time')
                ->get()
            : collect();

        $classSubjects = $teacher
            ? ClassSubject::with(['class', 'subject'])
                ->withCount(['materials', 'assignments'])
                ->where('teacher_id', $teacher->id)
                ->get()
            : collect();

        if ($teacher) {
            $classSubjectIds = $classSubjects->pluck('id');
        }

        $activeAssignments = $classSubjectIds->isNotEmpty()
            ? Assignment::with('classSubject.class', 'classSubject.subject')
                ->whereIn('class_subject_id', $classSubjectIds)
                ->where('is_published', true)
                ->where(fn ($q) => $q->whereNull('due_date')->orWhere('due_date', '>=', now()))
                ->orderBy('due_date')
                ->limit(8)
                ->get()
            : collect();

        $activeQuizzes = collect();

        $recentMaterials = $classSubjectIds->isNotEmpty()
            ? Material::with('classSubject.class', 'classSubject.subject')
                ->whereIn('class_subject_id', $classSubjectIds)
                ->where('is_published', true)
                ->latest()
                ->limit(6)
                ->get()
            : collect();

        $myStudentCount = $classSubjects->isNotEmpty()
            ? Student::active()->whereIn('class_id', $classSubjects->pluck('class_id')->unique())->count()
            : 0;

        $pendingGrading = $classSubjectIds->isNotEmpty()
            ? AssignmentSubmission::whereHas('assignment', fn ($q) => $q->whereIn('class_subject_id', $classSubjectIds))
                ->whereNotNull('submitted_at')
                ->whereDoesntHave('grade')
                ->count()
            : 0;

        return [
            'teacher' => $teacher,
            'today_schedules' => $todaySchedules,
            'class_subjects' => $classSubjects,
            'active_assignments' => $activeAssignments,
            'active_quizzes' => $activeQuizzes,
            'recent_materials' => $recentMaterials,
            'class_attendance' => $this->attendanceService->getClassAttendanceSummary($today)
                ->filter(fn ($row) => $classSubjects->pluck('class_id')->contains($row['class']?->id))
                ->values(),
            'stats' => [
                'classes' => $classSubjects->pluck('class_id')->unique()->count(),
                'students' => $myStudentCount,
                'assignments' => $activeAssignments->count(),
                'quizzes' => $activeQuizzes->count(),
                'materials' => $recentMaterials->count(),
                'pending_grading' => $pendingGrading,
            ],
            'monthly_trend' => $this->attendanceService->getMonthlyTrend(),
        ];
    }

    private function buildSiswaDashboard(User $user): array
    {
        $student = $user->student;
        $dayOfWeek = (string) now()->format('N');
        $classSubjectIds = collect();

        if ($student?->class_id) {
            $activeYear = AcademicYear::active();
            $query = ClassSubject::where('class_id', $student->class_id);
            if ($activeYear) {
                $query->where('academic_year_id', $activeYear->id);
            }
            $classSubjectIds = $query->pluck('id');
        }

        $todaySchedules = $student?->class_id
            ? Schedule::with('teacher.subjects')
                ->where('class_id', $student->class_id)
                ->where('day_of_week', $dayOfWeek)
                ->orderBy('start_time')
                ->get()
            : collect();

        $recentMaterials = $classSubjectIds->isNotEmpty()
            ? Material::with('classSubject.subject')
                ->whereIn('class_subject_id', $classSubjectIds)
                ->where('is_published', true)
                ->latest()
                ->limit(6)
                ->get()
            : collect();

        $activeAssignments = $classSubjectIds->isNotEmpty()
            ? Assignment::with('classSubject.subject')
                ->whereIn('class_subject_id', $classSubjectIds)
                ->where('is_published', true)
                ->where(fn ($q) => $q->whereNull('due_date')->orWhere('due_date', '>=', now()))
                ->orderBy('due_date')
                ->limit(6)
                ->get()
            : collect();

        $activeQuizzes = collect();

        $attendanceStats = $student
            ? $this->studentAttendanceStats($student)
            : ['hadir' => 0, 'terlambat' => 0, 'alfa' => 0, 'percentage' => 0];

        $grades = $student
            ? $this->studentGrades($student)
            : collect();

        $announcements = Announcement::with('creator:id,name')
            ->where(function ($q) use ($student) {
                $q->whereNull('class_id');
                if ($student?->class_id) {
                    $q->orWhere('class_id', $student->class_id);
                }
            })
            ->latest()
            ->limit(5)
            ->get();

        return [
            'student' => $student,
            'today_schedules' => $todaySchedules,
            'recent_materials' => $recentMaterials,
            'active_assignments' => $activeAssignments,
            'active_quizzes' => $activeQuizzes,
            'attendance_stats' => $attendanceStats,
            'grades' => $grades,
            'announcements' => $announcements,
        ];
    }

    private function studentAttendanceStats(Student $student): array
    {
        $month = now()->month;
        $year = now()->year;

        $counts = DB::table('attendance_details as ad')
            ->join('attendance as a', 'a.id', '=', 'ad.attendance_id')
            ->where('ad.student_id', $student->id)
            ->whereMonth('a.date', $month)
            ->whereYear('a.date', $year)
            ->selectRaw("
                SUM(ad.status = 'hadir') as hadir,
                SUM(ad.status = 'terlambat') as terlambat,
                SUM(ad.status = 'alfa') as alfa,
                COUNT(*) as total
            ")
            ->first();

        $total = (int) ($counts->total ?? 0);
        $hadir = (int) ($counts->hadir ?? 0);
        $terlambat = (int) ($counts->terlambat ?? 0);
        $present = $hadir + $terlambat;

        return [
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'alfa' => (int) ($counts->alfa ?? 0),
            'percentage' => $total > 0 ? round($present / $total * 100, 1) : 0,
        ];
    }

    private function studentGrades(Student $student): Collection
    {
        $reportCard = ReportCard::with(['details.subject'])
            ->where('student_id', $student->id)
            ->where('is_published', true)
            ->latest()
            ->first();

        if ($reportCard && $reportCard->details->isNotEmpty()) {
            return $reportCard->details->map(fn ($d) => [
                'subject' => $d->subject?->name ?? 'Mapel',
                'score' => $d->final_score,
                'grade' => $d->grade,
                'source' => 'raport',
            ]);
        }

        return collect();
    }
}

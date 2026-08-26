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
        $cacheKey = "user_dashboard_data_{$user->id}_" . today()->toDateString();
        
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 30, function () use ($user) {
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
        });
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

        $homeroomClass = $teacher ? $teacher->homeroomClasses()->first() : null;
        $homeroomAttendanceToday = null;
        if ($homeroomClass) {
            $hAtt = Attendance::with('details')->where('class_id', $homeroomClass->id)
                ->whereDate('date', $today)
                ->whereIn('session', ['pagi', 'siang', 'harian'])
                ->first();

            $totalStudents = Student::active()->where('class_id', $homeroomClass->id)->count();
            $hadir = 0; $sakit = 0; $izin = 0; $alfa = 0; $terlambat = 0;
            if ($hAtt) {
                foreach ($hAtt->details as $d) {
                    match ($d->status) {
                        'hadir' => $hadir++,
                        'terlambat' => $terlambat++,
                        'sakit' => $sakit++,
                        'izin' => $izin++,
                        'alfa' => $alfa++,
                        default => null,
                    };
                }
            }
            $totalPresent = $hadir + $terlambat;
            $percentage = $totalStudents > 0 ? (int) round($totalPresent / $totalStudents * 100) : 0;
            $homeroomAttendanceToday = [
                'class' => $homeroomClass,
                'total' => $totalStudents,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'sakit' => $sakit,
                'izin' => $izin,
                'alfa' => $alfa,
                'present_total' => $totalPresent,
                'percentage' => $percentage,
                'has_recorded' => !is_null($hAtt),
            ];
        }

        $homeroomClassIds = $teacher ? $teacher->homeroomClasses()->pluck('classes.id') : collect();
        $relevantClassIds = $classSubjects->pluck('class_id')->concat($homeroomClassIds)->unique();

        $classAttendance = $this->attendanceService->getClassAttendanceSummary($today)
            ->filter(fn ($row) => $relevantClassIds->contains($row['class']?->id))
            ->values();

        $avgAttendancePct = $classAttendance->isNotEmpty()
            ? (int) round($classAttendance->avg('percentage'))
            : 0;

        return [
            'teacher' => $teacher,
            'today_schedules' => $todaySchedules,
            'class_subjects' => $classSubjects,
            'active_assignments' => $activeAssignments,
            'active_quizzes' => $activeQuizzes,
            'recent_materials' => $recentMaterials,
            'class_attendance' => $classAttendance,
            'homeroom_attendance_today' => $homeroomAttendanceToday,
            'stats' => [
                'classes' => $relevantClassIds->count() ?: $classSubjects->pluck('class_id')->unique()->count(),
                'students' => $myStudentCount,
                'assignments' => $activeAssignments->count(),
                'quizzes' => $activeQuizzes->count(),
                'materials' => $recentMaterials->count(),
                'pending_grading' => $pendingGrading,
                'attendance_percentage' => $homeroomAttendanceToday ? $homeroomAttendanceToday['percentage'] : $avgAttendancePct,
            ],
            'monthly_trend' => $this->attendanceService->getMonthlyTrend(),
            'homeroom_class' => $homeroomClass,
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

        if ($todaySchedules->isNotEmpty() && $student) {
            $todayAttendances = Attendance::with('details')
                ->where('class_id', $student->class_id)
                ->whereDate('date', today())
                ->get();

            $homeroomAtt = $todayAttendances->first(fn ($a) => in_array($a->session, ['pagi', 'siang', 'harian'], true));
            $homeroomStatus = null;
            if ($homeroomAtt) {
                $hDet = $homeroomAtt->details->firstWhere('student_id', $student->id);
                if ($hDet && in_array($hDet->status, ['sakit', 'izin'], true)) {
                    $homeroomStatus = $hDet->status;
                }
            }

            foreach ($todaySchedules as $idx => $sched) {
                $slotNum = $idx + 1;
                $sessionKey = "mapel_{$slotNum}";
                $subName = $sched->subject;

                $att = $todayAttendances->first(function ($a) use ($sessionKey, $subName, $sched) {
                    if ($a->session === $sessionKey) return true;
                    if ($sched->class_subject_id && $a->class_subject_id == $sched->class_subject_id) return true;
                    if ($subName && stripos($a->notes ?? '', $subName) !== false) return true;
                    return false;
                });

                $studentStatus = null;
                if ($att) {
                    $det = $att->details->firstWhere('student_id', $student->id);
                    if ($det) {
                        $studentStatus = $det->status;
                    }
                }

                if (!$studentStatus && $homeroomStatus) {
                    $studentStatus = $homeroomStatus;
                }

                $sched->today_attendance_status = $studentStatus;
            }
        }

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
}

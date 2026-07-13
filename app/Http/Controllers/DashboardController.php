<?php

namespace App\Http\Controllers;

use App\Services\{AttendanceService, DashboardService};
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
        private readonly DashboardService $dashboardService,
    ) {
    }

    public function index()
    {
        $user = auth()->user();
        $data = $this->dashboardService->buildFor($user);

        return view($this->dashboardService->viewFor($user), $data);
    }

    public function elearning()
    {
        $this->authorize('viewAny', \App\Models\ClassSubject::class);

        $user = auth()->user();
        $activeYear = \App\Models\AcademicYear::active();

        if ($user->isGuru()) {
            $teacher = $user->teacher;
            if ($teacher) {
                $query = \App\Models\ClassSubject::with(['class.major', 'subject', 'teacher'])
                    ->withCount(['materials', 'assignments'])
                    ->where('teacher_id', $teacher->id);
                if ($activeYear) {
                    $query->where('academic_year_id', $activeYear->id);
                }
                $classSubjects = $query->get();
            } else {
                $classSubjects = collect();
            }
        } elseif ($user->isSiswa()) {
            $student = $user->student;
            if ($student?->class_id) {
                $query = \App\Models\ClassSubject::with(['class.major', 'subject', 'teacher'])
                    ->withCount(['materials', 'assignments'])
                    ->where('class_id', $student->class_id);
                if ($activeYear) {
                    $query->where('academic_year_id', $activeYear->id);
                }
                $classSubjects = $query->get();
            } else {
                $classSubjects = collect();
            }
        } else {
            $query = \App\Models\ClassSubject::with(['class.major', 'subject', 'teacher'])
                ->withCount(['materials', 'assignments']);
            if ($activeYear) {
                $query->where('academic_year_id', $activeYear->id);
            }
            $classSubjects = $query->get();
        }

        // Sort by class grade, class name, and subject name
        $classSubjects = $classSubjects->sortBy([
            ['class.grade', 'asc'],
            ['class.name', 'asc'],
            ['subject.name', 'asc'],
        ]);

        $teachers = $classSubjects->pluck('teacher')->filter()->unique('id')->sortBy('name')->values();

        return view('elearning.index', compact('classSubjects', 'teachers'));
    }

    public function chartData(Request $request)
    {
        $year = (int) $request->get('year', now()->year);

        return response()->json($this->attendanceService->getMonthlyChartData($year));
    }

    public function lmsActivityChartData()
    {
        return response()->json(
            $this->dashboardService->getLmsActivityTrend(7)
        );
    }
}

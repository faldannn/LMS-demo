<?php

namespace App\Http\Controllers;

use App\Services\{AttendanceService, DashboardService, ReportCardService};
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
        private readonly ReportCardService $reportCardService,
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

        if ($user->isGuru()) {
            $teacher = $user->teacher;
            $classSubjects = $teacher
                ? \App\Models\ClassSubject::with(['class', 'subject', 'teacher'])
                    ->withCount(['materials', 'assignments'])
                    ->where('teacher_id', $teacher->id)
                    ->get()
                : collect();
        } elseif ($user->isSiswa()) {
            $student = $user->student;
            $classSubjects = $student?->class_id
                ? \App\Models\ClassSubject::with(['class', 'subject', 'teacher'])
                    ->withCount(['materials', 'assignments'])
                    ->where('class_id', $student->class_id)
                    ->get()
                : collect();
        } else {
            $classSubjects = \App\Models\ClassSubject::with(['class', 'subject', 'teacher'])
                ->withCount(['materials', 'assignments'])
                ->get();
        }

        return view('elearning.index', compact('classSubjects'));
    }

    public function chartData(Request $request)
    {
        $year = (int) $request->get('year', now()->year);

        return response()->json($this->attendanceService->getMonthlyChartData($year));
    }

    public function gradeChartData(Request $request)
    {
        $semester = $request->get('semester', 'ganjil');

        return response()->json(
            $this->reportCardService->getClassGradeAverages($semester)
        );
    }

    public function lmsActivityChartData()
    {
        return response()->json(
            $this->dashboardService->getLmsActivityTrend(7)
        );
    }
}

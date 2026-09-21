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
                $query = \App\Models\ClassSubject::whereHas('class')
                    ->with(['class.major', 'subject', 'teacher'])
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
                $query = \App\Models\ClassSubject::whereHas('class')
                    ->with(['class.major', 'subject', 'teacher'])
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
            $query = \App\Models\ClassSubject::whereHas('class')
                ->with(['class.major', 'subject', 'teacher'])
                ->withCount(['materials', 'assignments']);
            if ($activeYear) {
                $query->where('academic_year_id', $activeYear->id);
            }
            $classSubjects = $query->get();
        }

        $todayAttendanceMap = [];
        if ($user->isSiswa() && isset($student) && $student?->class_id) {
            $dayOfWeek = today()->dayOfWeekIso;
            $daySchedules = \App\Models\Schedule::where('class_id', $student->class_id)
                ->where('day_of_week', $dayOfWeek)
                ->orderBy('start_time')
                ->get();

            $todayAttendances = \App\Models\Attendance::with('details')
                ->where('class_id', $student->class_id)
                ->whereDate('date', today())
                ->get();

            $homeroomAtt = $todayAttendances->first(fn($a) => in_array($a->session, ['pagi', 'siang', 'harian'], true));
            $homeroomStatus = null;
            if ($homeroomAtt) {
                $hDet = $homeroomAtt->details->firstWhere('student_id', $student->id);
                if ($hDet && in_array($hDet->status, ['sakit', 'izin'], true)) {
                    $homeroomStatus = $hDet->status;
                }
            }

            foreach ($classSubjects as $cs) {
                $subName = $cs->subject?->name;
                $slotNum = null;
                $hasScheduleToday = false;
                $timeScheduleStr = '';

                if ($daySchedules->isNotEmpty() && $subName) {
                    foreach ($daySchedules as $idx => $sched) {
                        if ($sched->subject === $subName || stripos($sched->subject, $subName) !== false) {
                            $slotNum = $idx + 1;
                            $hasScheduleToday = true;
                            if ($sched->start_time && $sched->end_time) {
                                $timeScheduleStr = $sched->start_time->format('H:i') . ' - ' . $sched->end_time->format('H:i');
                            }
                            break;
                        }
                    }
                }

                $sessionKey = $slotNum ? "mapel_{$slotNum}" : null;

                $att = $todayAttendances->first(function ($a) use ($sessionKey, $subName, $cs) {
                    if ($sessionKey && $a->session === $sessionKey) return true;
                    if ($cs && $a->class_subject_id == $cs->id) return true;
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

                $todayAttendanceMap[$cs->id] = [
                    'has_schedule_today' => $hasScheduleToday,
                    'time_str' => $timeScheduleStr,
                    'status' => $studentStatus,
                ];
            }
        }

        return view('elearning.index', compact('classSubjects', 'todayAttendanceMap'));
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

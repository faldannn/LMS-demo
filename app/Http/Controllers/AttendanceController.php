<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use App\Models\{Attendance, AttendanceDetail, Classes, Student};
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class AttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $service)
    {
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->isSiswa() && $user->student) {
            $classId = $user->student->class_id;
            $classes = collect([$user->student->class]);
        } else {
            $classId = $request->class_id;
            $classes = Classes::orderBy('name')->get();
        }

        if ($user->isKepalaSekolah()) {
            $viewMode = $request->get('view', 'grid') === 'list' ? 'list' : 'grid';

            $classSummaries = Classes::withCount([
                    'students as active_students_count' => fn ($q) => $q->where('status', true),
                ])
                ->with([
                    'homeroomTeacher:id,name',
                    'attendances' => fn ($q) => $this->applyAttendanceFilters($q, $request)
                        ->with(['teacher:id,name,nip', 'details'])
                        ->orderByDesc('date')
                        ->orderByDesc('id'),
                ])
                ->when($classId, fn ($q) => $q->where('id', $classId))
                ->orderBy('name')
                ->get()
                ->map(function (Classes $class) {
                    $attendances = $class->attendances;
                    $details = $attendances->flatMap->details;
                    $present = $details->whereIn('status', ['hadir', 'terlambat'])->count();
                    $total = $details->count();

                    $class->attendance_summary = [
                        'sessions' => $attendances->count(),
                        'students' => $class->active_students_count,
                        'hadir' => $details->where('status', 'hadir')->count(),
                        'terlambat' => $details->where('status', 'terlambat')->count(),
                        'izin_sakit' => $details->whereIn('status', ['izin', 'sakit'])->count(),
                        'alfa' => $details->where('status', 'alfa')->count(),
                        'percentage' => $total > 0 ? round($present / $total * 100, 1) : 0,
                    ];
                    $class->latest_attendance = $attendances->first();

                    return $class;
                });

            return view('attendance.index', compact('classes', 'classSummaries', 'viewMode'));
        }

        $query = Attendance::with(['class', 'teacher', 'details'])
            ->when($classId, fn($q) => $q->where('class_id', $classId))
            ->orderByDesc('date');

        $this->applyAttendanceFilters($query, $request);

        $attendances = $query->paginate(20)->withQueryString();

        return view('attendance.index', compact('attendances', 'classes'));
    }

    public function create(Request $request)
    {
        $classes = Classes::orderBy('name')->get(['id', 'name', 'grade']);
        $date = $request->get('date', today()->toDateString());
        $classId = $request->get('class_id');

        $students = $classId
            ? Student::forClass($classId)->active()->orderBy('name')->get()
            : collect();

        // Load existing attendance for this session
        $existing = [];
        if ($classId && $date) {
            $att = Attendance::where(['date' => $date, 'class_id' => $classId])->first();
            if ($att) {
                $existing = $att->details()->pluck('status', 'student_id')->toArray();
            }
        }

        return view('attendance.create', compact('classes', 'students', 'date', 'classId', 'existing'));
    }

    public function store(StoreAttendanceRequest $request)
    {
        try {
            $this->service->storeAttendance($request->validated(), Auth::user()->teacher);
            
            return redirect()->route('attendance.index')
                ->with('success', 'Absensi berhasil disimpan!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan absensi: ' . $e->getMessage());
        }
    }

    public function show(Attendance $attendance, Request $request)
    {
        $attendance->load(['class', 'teacher', 'details.student']);

        $periodType = $request->get('period', 'week') === 'month' ? 'month' : 'week';
        $periodAnchor = $periodType === 'month'
            ? Carbon::createFromFormat('Y-m', $request->get('month', $attendance->date->format('Y-m')))->startOfMonth()
            : Carbon::parse($request->get('week', $attendance->date->toDateString()))->startOfWeek();

        $periodStart = $periodAnchor->copy();
        $periodEnd = $periodType === 'month'
            ? $periodAnchor->copy()->endOfMonth()
            : $periodAnchor->copy()->endOfWeek();

        $periodAttendances = Attendance::with(['details.student', 'teacher'])
            ->where('class_id', $attendance->class_id)
            ->whereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->orderByDesc('date')
            ->get();

        $periodDetails = $periodAttendances->flatMap->details;
        $periodStats = [
            'sessions' => $periodAttendances->count(),
            'hadir' => $periodDetails->where('status', 'hadir')->count(),
            'terlambat' => $periodDetails->where('status', 'terlambat')->count(),
            'alfa' => $periodDetails->where('status', 'alfa')->count(),
            'izin_sakit' => $periodDetails->whereIn('status', ['sakit', 'izin'])->count(),
        ];

        $studentRecaps = $periodDetails
            ->groupBy('student_id')
            ->map(function ($details) {
                $student = $details->first()->student;

                return [
                    'student' => $student,
                    'hadir' => $details->where('status', 'hadir')->count(),
                    'terlambat' => $details->where('status', 'terlambat')->count(),
                    'izin_sakit' => $details->whereIn('status', ['sakit', 'izin'])->count(),
                    'alfa' => $details->where('status', 'alfa')->count(),
                    'total' => $details->count(),
                ];
            })
            ->sortBy(fn ($row) => $row['student']?->name ?? '')
            ->values();

        return view('attendance.show', compact(
            'attendance',
            'periodType',
            'periodStart',
            'periodEnd',
            'periodAttendances',
            'periodStats',
            'studentRecaps',
        ));
    }

    public function edit(Attendance $attendance)
    {
        $attendance->load(['details.student', 'class.students']);
        $existingDetails = $attendance->details->keyBy('student_id');
        return view('attendance.edit', compact('attendance', 'existingDetails'));
    }

    public function update(StoreAttendanceRequest $request, Attendance $attendance)
    {
        try {
            $this->service->updateAttendance($attendance, $request->validated());

            return redirect()->route('attendance.show', $attendance)
                ->with('success', 'Absensi berhasil diperbarui!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui absensi: ' . $e->getMessage());
        }
    }

    public function myAttendance(Request $request)
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(404, 'Data siswa tidak ditemukan. Hubungi administrator.');
        }

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $history = $student->attendanceDetails()
            ->with('attendance')
            ->whereHas(
                'attendance',
                fn($q) =>
                $q->whereMonth('date', $month)->whereYear('date', $year)
            )
            ->orderByDesc('id')
            ->get();

        $stats = $history->groupBy('status')
            ->map->count();

        return view('attendance.my-attendance', compact('student', 'history', 'stats', 'month', 'year'));
    }

    private function applyAttendanceFilters($query, Request $request)
    {
        return $query
            ->when($request->date, fn ($q) => $q->whereDate('date', $request->date))
            ->when($request->month, function ($q) use ($request) {
                $month = Carbon::createFromFormat('Y-m', $request->month);

                $q->whereMonth('date', $month->month)
                    ->whereYear('date', $month->year);
            });
    }
}

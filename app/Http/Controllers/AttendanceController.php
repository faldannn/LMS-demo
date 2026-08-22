<?php

namespace App\Http\Controllers;

use App\Exports\StudentAttendanceExport;
use App\Exports\TeacherAttendanceExport;
use App\Http\Requests\StoreAttendanceRequest;
use App\Models\{Attendance, AttendanceDetail, Classes, Student, Teacher, TeacherAttendance, SelfAttendanceLog, Schedule, ClassSubject, Setting};
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class AttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $service)
    {
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $activeTab = $request->get('tab', 'students'); // 'students' or 'teachers'

        [$sDate, $eDate] = $this->resolveDateBounds($request);
        $isSingleDay = $sDate->equalTo($eDate);

        if ($user->isSiswa() && $user->student) {
            $classId = $user->student->class_id;
            $classes = collect([$user->student->class]);
        } elseif ($user->isGuru()) {
            $teacher = $user->teacher;
            if ($activeTab === 'students' && !$request->has('tab')) {
                if ($teacher) {
                    $taughtClassIds = $teacher->classSubjects()->pluck('class_id');
                    $scheduledClassIds = $teacher->schedules()->pluck('class_id');
                    $homeroomClassIds = $teacher->homeroomClasses()->pluck('id');
                    $allClassIds = $taughtClassIds->merge($scheduledClassIds)->merge($homeroomClassIds)->unique()->filter();
                    $classes = Classes::whereIn('id', $allClassIds)->orderBy('name')->get();
                } else {
                    $classes = collect();
                }
                
                if ($classes->isEmpty()) {
                    return redirect()->route('attendance.create');
                }

                $classId = $request->class_id ?: $classes->first()->id;
                $latestAtt = Attendance::where('class_id', $classId)
                    ->whereIn('session', ['pagi', 'siang', 'harian'])
                    ->orderByDesc('date')
                    ->first();

                if (!$latestAtt) {
                    $latestAtt = Attendance::firstOrCreate(
                        [
                            'date' => today()->toDateString(),
                            'class_id' => $classId,
                            'session' => 'pagi',
                        ],
                        [
                            'teacher_id' => $teacher?->id ?? Teacher::active()->first()?->id,
                        ]
                    );
                }

                return redirect()->route('attendance.show', array_merge(['attendance' => $latestAtt->id], $request->query()));
            } else {
                $classId = $request->class_id;
                $classes = Classes::orderBy('name')->get();
            }
        } else {
            $classId = $request->class_id;
            $classes = Classes::orderBy('name')->get();
        }

        // --- Data Presensi Guru (Admin & Kepala Sekolah) ---
        $teachers = Teacher::active()->orderBy('name')->get();
        $rawTeacherAttendances = TeacherAttendance::with(['teacher', 'user'])
            ->whereBetween('date', [$sDate->toDateString(), $eDate->toDateString()])
            ->orderBy('date', 'desc')
            ->get();

        $teacherLogsGrouped = $rawTeacherAttendances->groupBy('teacher_id');
        $teacherAttendances = $rawTeacherAttendances->keyBy('teacher_id');

        if ($isSingleDay) {
            $selectedDateLabel = $sDate->translatedFormat('d F Y');
        } else {
            $selectedDateLabel = $sDate->translatedFormat('d M Y') . ' s/d ' . $eDate->translatedFormat('d M Y');
        }

        $teacherSummary = [
            'total' => $teachers->count(),
            'hadir' => $rawTeacherAttendances->where('status', 'hadir')->count(),
            'terlambat' => $rawTeacherAttendances->where('status', 'terlambat')->count(),
            'izin_sakit' => $rawTeacherAttendances->whereIn('status', ['izin', 'sakit'])->count(),
            'alfa' => $rawTeacherAttendances->where('status', 'alfa')->count(),
            'checked_out' => $rawTeacherAttendances->whereNotNull('check_out_at')->count(),
        ];

        // --- Data Self Attendance Logs Siswa dengan Foto ---
        $studentSelfLogs = SelfAttendanceLog::with(['student.class'])
            ->whereNotNull('photo_path')
            ->whereBetween('attendance_date', [$sDate->toDateString(), $eDate->toDateString()])
            ->latest('checked_in_at')
            ->get();

        if ($user->isKepalaSekolah() || $user->isAdmin()) {
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

            return view('attendance.index', compact(
                'classes',
                'classSummaries',
                'viewMode',
                'activeTab',
                'teachers',
                'teacherAttendances',
                'teacherLogsGrouped',
                'teacherSummary',
                'studentSelfLogs',
                'selectedDateLabel',
                'isSingleDay',
                'sDate',
                'eDate'
            ));
        }

        $query = Attendance::with(['class', 'teacher', 'details.student', 'details.attendance.teacher'])
            ->when($classId, fn($q) => $q->where('class_id', $classId))
            ->orderByDesc('date');

        if ($user->isGuru()) {
            $query->whereIn('class_id', $classes->pluck('id'));
        }

        $this->applyAttendanceFilters($query, $request);

        $attendances = $query->paginate(20)->withQueryString();

        return view('attendance.index', compact(
            'attendances',
            'classes',
            'activeTab',
            'teachers',
            'teacherAttendances',
            'teacherLogsGrouped',
            'teacherSummary',
            'studentSelfLogs',
            'selectedDateLabel',
            'isSingleDay',
            'sDate',
            'eDate'
        ));
    }

    public function exportStudentsExcel(Request $request)
    {
        $period = $request->get('period', 'month');
        $date = $request->get('date');
        $month = $request->get('month');
        $classId = $request->filled('class_id') ? (int) $request->class_id : null;
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $fileName = 'rekap_absensi_siswa_' . ($classId ? 'kelas_' . $classId . '_' : '') . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new StudentAttendanceExport($period, $date, $month, $classId, $startDate, $endDate),
            $fileName
        );
    }

    public function exportTeachersExcel(Request $request)
    {
        $period = $request->get('period', 'month');
        $date = $request->get('date');
        $month = $request->get('month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $fileName = 'rekap_absensi_guru_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new TeacherAttendanceExport($period, $date, $month, $startDate, $endDate),
            $fileName
        );
    }

    public function create(Request $request)
    {
        if (!Setting::isFeatureEnabled('subject_attendance')) {
            return redirect()->route('dashboard')->with('error', 'Fitur Presensi Mata Pelajaran sedang dinonaktifkan oleh Superadmin.');
        }

        if ($request->has('ref') || $request->has('token')) {
            try {
                $decrypted = \Illuminate\Support\Facades\Crypt::decryptArray($request->get('ref') ?: $request->get('token'));
                $request->merge($decrypted);
            } catch (\Throwable $e) {
                // Abaikan jika token tidak valid
            }
        }

        $user = auth()->user();
        if ($user->isGuru()) {
            $teacher = $user->teacher;
            if ($teacher) {
                $taughtClassIds = $teacher->classSubjects()->pluck('class_id');
                $scheduledClassIds = $teacher->schedules()->pluck('class_id');
                $homeroomClassIds = $teacher->homeroomClasses()->pluck('id');
                
                $allClassIds = $taughtClassIds->merge($scheduledClassIds)->merge($homeroomClassIds)->unique()->filter();

                if ($allClassIds->isNotEmpty()) {
                    $classes = Classes::whereIn('id', $allClassIds)->orderBy('name')->get(['id', 'name', 'grade']);
                } else {
                    $classes = collect();
                }
            } else {
                $classes = collect();
            }
        } else {
            $classes = Classes::orderBy('name')->get(['id', 'name', 'grade']);
        }
        $classId = $request->get('class_id');

        if (!$classId && $user->isGuru() && $classes->isNotEmpty()) {
            $classId = $classes->first()->id;
        }

        if ($classId && !$classes->pluck('id')->contains($classId)) {
            $classId = null;
        }

        $hasSaturday = $this->hasSaturdaySchedule($classId);
        $period = $request->get('period');
        if ($period && in_array($period, ['today', 'week', '30_days', 'last_30_days', 'month'], true)) {
            [$sDateObj, $eDateObj] = $this->resolveDateBounds($request);
            $startDate = $sDateObj->toDateString();
            $endDate = $eDateObj->toDateString();
        } else {
            $startDate = $request->get('start_date', $request->get('date', today()->toDateString()));
            $endDate = $request->get('end_date', $startDate);
            $weekStartStr = today()->startOfWeek(Carbon::MONDAY)->toDateString();
            $weekEndStr = today()->endOfWeek($hasSaturday ? Carbon::SATURDAY : Carbon::FRIDAY)->toDateString();

            if ($startDate === today()->toDateString() && $endDate === today()->toDateString()) {
                $period = 'today';
            } elseif ($startDate === today()->subDays(30)->toDateString() && $endDate === today()->toDateString()) {
                $period = '30_days';
            } elseif ($startDate === $weekStartStr && $endDate === $weekEndStr) {
                $period = 'week';
            } elseif ($startDate === today()->startOfMonth()->toDateString() && $endDate === today()->endOfMonth()->toDateString()) {
                $period = 'month';
            } else {
                $period = 'custom';
            }
        }
        $date = $startDate;

        $selectedClass = $classId ? Classes::find($classId) : null;
        $isRangeMode = $startDate !== $endDate;
        $session = $request->get('session');
        if ($session && preg_match('/day_\d+_(mapel_\d+)/', $session, $matches)) {
            $session = $matches[1];
        }
        if (!$session) {
            $session = $isRangeMode ? 'all' : 'mapel_1';
        }
        if (in_array($session, ['pagi', 'siang', 'harian'], true)) {
            $session = 'mapel_1';
        }


        $isTeacher = $user->isGuru();
        $teacherObj = $user->teacher;
        $isAdmin = $user->isAdmin() || $user->isSuperAdmin();
        $isGuruOnly = $isTeacher && !$isAdmin && $teacherObj;

        // Mapel & Jadwal Sesi per Hari
        $sessionLabels = [];
        $selectedSubjectName = null;
        $selectedSubjectInfo = null;

        if ($classId) {
            $cDate = Carbon::parse($startDate);
            $dayOfWeek = $cDate->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
            $dayName = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][$dayOfWeek] ?? "Hari-{$dayOfWeek}";
            $isWeekend = in_array($dayOfWeek, [6, 7], true);

            $daySchedules = Schedule::with('teacher')
                ->where('class_id', $classId)
                ->where('day_of_week', $dayOfWeek)
                ->orderBy('start_time')
                ->get();

            $allSchedules = Schedule::with('teacher')
                ->where('class_id', $classId)
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get();

            if ($daySchedules->isNotEmpty() && !$isRangeMode && !$isWeekend) {
                foreach ($daySchedules as $idx => $sched) {
                    $slotNum = $idx + 1;
                    $key = "mapel_{$slotNum}";
                    $subName = $sched->subject;
                    $tName = $sched->teacher?->name;
                    $tId = $sched->teacher_id;
                    $timeStr = ($sched->start_time && $sched->end_time)
                        ? $sched->start_time->format('H:i') . ' - ' . $sched->end_time->format('H:i')
                        : '';

                    if ($isGuruOnly && $teacherObj && $tId != $teacherObj->id) {
                        continue;
                    }

                    $sessionLabels[$key] = [
                        'label' => "Jam {$slotNum}: {$subName}" . ($timeStr ? " ({$timeStr})" : "") . ($tName && !$isGuruOnly ? " — {$tName}" : ""),
                        'short_label' => "Jam {$slotNum} ({$timeStr})",
                        'subject' => $subName,
                        'teacher' => $tName,
                        'teacher_id' => $tId,
                        'slot_num' => $slotNum,
                        'time_str' => $timeStr,
                        'day_name' => $dayName,
                    ];
                }
            }

            if (empty($sessionLabels)) {
                if ($isGuruOnly && $teacherObj) {
                    $teacherSchedules = $allSchedules->where('teacher_id', $teacherObj->id);
                    foreach ($teacherSchedules as $sched) {
                        $dName = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][$sched->day_of_week] ?? '';
                        $daySlots = $allSchedules->where('day_of_week', $sched->day_of_week)->values();
                        $slotNum = $daySlots->search(fn($s) => $s->id == $sched->id) !== false ? ($daySlots->search(fn($s) => $s->id == $sched->id) + 1) : 1;
                        $key = "sched_{$sched->id}";
                        $subName = $sched->subject;
                        $timeStr = ($sched->start_time && $sched->end_time)
                            ? $sched->start_time->format('H:i') . ' - ' . $sched->end_time->format('H:i')
                            : '';

                        $sessionLabels[$key] = [
                            'raw_session' => "mapel_{$slotNum}",
                            'label' => "{$dName} · Jam {$slotNum}: {$subName}" . ($timeStr ? " ({$timeStr})" : ""),
                            'short_label' => "{$dName} Jam {$slotNum}",
                            'subject' => $subName,
                            'teacher' => $teacherObj->name,
                            'teacher_id' => $teacherObj->id,
                            'slot_num' => $slotNum,
                            'time_str' => $timeStr,
                            'day_name' => $dName,
                        ];
                    }
                } else {
                    foreach ($allSchedules as $sched) {
                        $dName = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][$sched->day_of_week] ?? '';
                        $daySlots = $allSchedules->where('day_of_week', $sched->day_of_week)->values();
                        $slotNum = $daySlots->search(fn($s) => $s->id == $sched->id) !== false ? ($daySlots->search(fn($s) => $s->id == $sched->id) + 1) : 1;
                        $key = "sched_{$sched->id}";
                        $subName = $sched->subject;
                        $tName = $sched->teacher?->name;
                        $timeStr = ($sched->start_time && $sched->end_time)
                            ? $sched->start_time->format('H:i') . ' - ' . $sched->end_time->format('H:i')
                            : '';

                        $sessionLabels[$key] = [
                            'raw_session' => "mapel_{$slotNum}",
                            'label' => "{$dName} · Jam {$slotNum}: {$subName}" . ($tName ? " — {$tName}" : "") . ($timeStr ? " ({$timeStr})" : ""),
                            'short_label' => "{$dName} Jam {$slotNum}",
                            'subject' => $subName,
                            'teacher' => $tName,
                            'teacher_id' => $sched->teacher_id,
                            'slot_num' => $slotNum,
                            'time_str' => $timeStr,
                            'day_name' => $dName,
                        ];
                    }
                }
            }


            if (empty($sessionLabels)) {
                $sessionLabels['mapel_1'] = [
                    'label' => 'Tidak ada jadwal mengajar pada hari terpilih',
                    'short_label' => 'Tidak Ada Jadwal',
                    'subject' => null,
                    'teacher' => null,
                    'slot_num' => 1,
                    'time_str' => '',
                    'day_name' => $dayName,
                ];
            }

            $allOption = [
                'all' => [
                    'raw_session' => 'all',
                    'label' => $isGuruOnly ? 'Semua Mapel Saya (Gabungan)' : 'Semua Mata Pelajaran (Gabungan)',
                    'short_label' => 'Semua Mapel',
                    'subject' => null,
                    'teacher' => null,
                    'slot_num' => 0,
                    'time_str' => '',
                    'day_name' => '',
                ]
            ];
            $sessionLabels = array_merge($allOption, $sessionLabels);

            if ($session !== 'all' && !isset($sessionLabels[$session])) {
                $session = array_key_first($sessionLabels) ?: 'all';
            }

            $selectedSubjectInfo = $sessionLabels[$session] ?? reset($sessionLabels);
            $selectedSubjectName = $selectedSubjectInfo['subject'] ?? null;
        } else {
            for ($m = 1; $m <= 8; $m++) {
                $sessionLabels["mapel_{$m}"] = [
                    'label' => "Jam Mapel {$m}",
                    'subject' => null,
                    'teacher' => null,
                ];
            }
        }

        $students = $classId
            ? Student::forClass($classId)->active()->orderBy('name')->get()
            : collect();

        $existing = [];
        $isRangeMode = $startDate !== $endDate;
        $rangeRecaps = [];
        $sessionNotes = '';

        if ($classId) {
            if ($isRangeMode || $session === 'all') {
                // Rentang Tanggal atau Sesi Gabungan: Ambil seluruh absensi dalam periode
                $rangeQuery = Attendance::with(['teacher', 'details.student', 'details.attendance.teacher'])
                    ->where('class_id', $classId)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->orderByDesc('date');

                if ($session && $session !== 'all') {
                    $subToFilter = $selectedSubjectName ?: $session;
                    $rangeQuery->where(function($q) use ($session, $subToFilter) {
                        $q->where('session', $session)
                          ->orWhere('notes', 'like', "%{$subToFilter}%");
                    });
                }

                if ($isGuruOnly && $teacherObj) {
                    $rangeQuery->where('teacher_id', $teacherObj->id);
                }

                $rangeAttendances = $rangeQuery->get();

                $allDetails = $rangeAttendances->flatMap->details;
                $scheduleCache = [];

                $datesInPeriod = $rangeAttendances->pluck('date')
                    ->map(fn($d) => $d->toDateString())
                    ->unique()
                    ->sort()
                    ->values();

                $startOfWeekAnchor = Carbon::parse($startDate)->startOfWeek(Carbon::MONDAY);

                foreach ($students as $student) {
                    $sDetails = $allDetails->where('student_id', $student->id);
                    $dailySummary = [];
                    foreach ($datesInPeriod as $dateStr) {
                        $dLogs = $sDetails->filter(fn($d) => $d->attendance?->date?->toDateString() === $dateStr);
                        $cDate = Carbon::parse($dateStr);
                        $dayShort = $cDate->translatedFormat('D');
                        $dateShort = $cDate->format('d/m');
                        $weekNum = (int) $startOfWeekAnchor->diffInWeeks($cDate->copy()->startOfWeek(Carbon::MONDAY)) + 1;
                        
                        if ($dLogs->isEmpty()) {
                            $status = 'none';
                        } elseif ($dLogs->contains('status', 'alfa')) {
                            $status = 'alfa';
                        } elseif ($dLogs->contains('status', 'terlambat')) {
                            $status = 'terlambat';
                        } elseif ($dLogs->contains('status', 'sakit') || $dLogs->contains('status', 'izin')) {
                            $status = 'sakit_izin';
                        } else {
                            $status = 'hadir';
                        }

                        $dailySummary[] = [
                            'week_num' => $weekNum,
                            'day' => $dayShort,
                            'date' => $dateShort,
                            'full_date' => "Minggu ke-{$weekNum} · {$cDate->translatedFormat('l, d F Y')}",
                            'status' => $status,
                            'count' => $dLogs->count(),
                        ];
                    }

                    $logsByDateMap = [];
                    foreach ($sDetails as $d) {
                        $cDate = $d->attendance?->date ? Carbon::parse($d->attendance->date) : null;
                        if (!$cDate) continue;

                        $dateStr = $cDate->toDateString();
                        $sessionKey = $d->attendance?->session ?? 'pagi';
                        $sessionFormatted = $this->resolveSessionSubjectLabel($classId, $dateStr, $sessionKey, $scheduleCache, $d->attendance);
                        $teacherName = $d->attendance?->teacher?->name ?? '-';

                        $sessWeight = match($sessionKey) {
                            'pagi' => 0,
                            'mapel_1' => 1,
                            'mapel_2' => 2,
                            'mapel_3' => 3,
                            'mapel_4' => 4,
                            'mapel_5' => 5,
                            'mapel_6' => 6,
                            'mapel_7' => 7,
                            'mapel_8' => 8,
                            'siang' => 9,
                            default => 10,
                        };

                        $weekNum = (int) $startOfWeekAnchor->diffInWeeks($cDate->copy()->startOfWeek(Carbon::MONDAY)) + 1;
                        $dayDate = $cDate->translatedFormat('l, d F Y');

                        if (!isset($logsByDateMap[$dateStr])) {
                            $logsByDateMap[$dateStr] = [
                                'raw_date' => $dateStr,
                                'week_num' => $weekNum,
                                'day_date' => $dayDate,
                                'pagi' => null,
                                'mapels' => [],
                            ];
                        }

                        $dayOfWeek = $cDate->dayOfWeekIso;
                        $cacheKey = "{$classId}_{$dayOfWeek}";
                        $sched = isset($scheduleCache[$cacheKey]) ? $scheduleCache[$cacheKey]->get($sessWeight - 1) : null;
                        $timeStr = ($sched?->start_time && $sched?->end_time)
                            ? $sched->start_time->format('H:i') . ' - ' . $sched->end_time->format('H:i') . ' WIB'
                            : '';

                        $itemData = [
                            'session_key' => $sessionKey,
                            'sess_weight' => $sessWeight,
                            'session' => $sessionFormatted,
                            'teacher_name' => $teacherName,
                            'time_str' => $timeStr,
                            'status' => $d->status,
                            'status_label' => match($d->status) {
                                'hadir' => '🟢 Hadir',
                                'terlambat' => '⏰ Terlambat',
                                'sakit' => '🤒 Sakit',
                                'izin' => '📄 Izin',
                                'alfa' => '🔴 Alfa',
                                default => ucfirst($d->status)
                            },
                            'check_in' => $d->check_in ? $d->check_in->format('H:i') . ' WIB' : '-',
                            'notes' => $d->notes ?: 'Tidak ada keterangan',
                        ];

                        if (in_array($sessionKey, ['pagi', 'harian', 'siang'], true)) {
                            $logsByDateMap[$dateStr]['pagi'] = $itemData;
                        } else {
                            $logsByDateMap[$dateStr]['mapels'][] = $itemData;
                        }
                    }

                    foreach ($logsByDateMap as &$dateItem) {
                        usort($dateItem['mapels'], fn($a, $b) => $a['sess_weight'] <=> $b['sess_weight']);
                    }
                    unset($dateItem);

                    ksort($logsByDateMap);
                    $logsByDate = array_values($logsByDateMap);

                    $rangeRecaps[$student->id] = [
                        'hadir' => $sDetails->where('status', 'hadir')->count(),
                        'alfa' => $sDetails->where('status', 'alfa')->count(),
                        'sakit_izin' => $sDetails->whereIn('status', ['sakit', 'izin'])->count(),
                        'terlambat' => $sDetails->where('status', 'terlambat')->count(),
                        'total' => $sDetails->count(),
                        'daily_summary' => $dailySummary,
                        'logs_by_date' => $logsByDate,
                        'logs' => $sDetails->sortBy(function ($d) {
                            $date = $d->attendance?->date ? $d->attendance->date->toDateString() : '0000-00-00';
                            $sess = $d->attendance?->session ?? '';
                            $weight = match($sess) {
                                'pagi' => 0,
                                'mapel_1' => 1,
                                'mapel_2' => 2,
                                'mapel_3' => 3,
                                'mapel_4' => 4,
                                'mapel_5' => 5,
                                'mapel_6' => 6,
                                'mapel_7' => 7,
                                'mapel_8' => 8,
                                'siang' => 9,
                                default => 10,
                            };
                            return $date . '_' . sprintf('%02d', $weight);
                        })->values()->map(function ($d) use ($classId, $startOfWeekAnchor, &$scheduleCache) {
                            $cDate = $d->attendance?->date ? Carbon::parse($d->attendance->date) : null;
                            $dateStr = $cDate?->toDateString();
                            $sessionKey = $d->attendance?->session ?? 'pagi';
                            $sessionFormatted = $dateStr 
                                ? $this->resolveSessionSubjectLabel($classId, $dateStr, $sessionKey, $scheduleCache, $d->attendance)
                                : ucfirst($sessionKey);

                            $sessWeight = match($sessionKey) {
                                'pagi' => 0,
                                'mapel_1' => 1,
                                'mapel_2' => 2,
                                'mapel_3' => 3,
                                'mapel_4' => 4,
                                'mapel_5' => 5,
                                'mapel_6' => 6,
                                'mapel_7' => 7,
                                'mapel_8' => 8,
                                'siang' => 9,
                                default => 10,
                            };

                            $weekNum = $cDate ? ((int) $startOfWeekAnchor->diffInWeeks($cDate->copy()->startOfWeek(Carbon::MONDAY)) + 1) : 1;
                            $dayDate = $cDate ? $cDate->translatedFormat('l, d F Y') : '-';
                            $formattedDate = $cDate ? "Minggu ke-{$weekNum} · {$dayDate}" : '-';

                            return [
                                'raw_date' => $dateStr,
                                'sess_weight' => $sessWeight,
                                'week_num' => $weekNum,
                                'day_date' => $dayDate,
                                'date' => $dayDate,
                                'formatted_date' => $formattedDate,
                                'session' => $sessionFormatted,
                                'status' => $d->status,
                                'status_label' => match($d->status) {
                                     'hadir' => '🟢 Hadir',
                                     'terlambat' => '⏰ Terlambat',
                                     'sakit' => '🤒 Sakit',
                                     'izin' => '📄 Izin',
                                     'alfa' => '🔴 Alfa (Tanpa Keterangan)',
                                     default => ucfirst($d->status)
                                 },
                                'check_in' => $d->check_in ? $d->check_in->format('H:i') . ' WIB' : '-',
                                'notes' => $d->notes ?: 'Tidak ada keterangan',
                            ];
                        })->values(),
                    ];
                }
            } else {
                // Satu Tanggal: Presensi Instant Auto-Save & Sinkronisasi
                $homeroomAtt = Attendance::with('details')->where('class_id', $classId)
                    ->where('date', $startDate)
                    ->whereIn('session', ['pagi', 'siang', 'harian'])
                    ->first();

                $homeroomDetails = $homeroomAtt ? $homeroomAtt->details->keyBy('student_id') : collect();

                $sessionNotes = '';
                $subjectModel = $selectedSubjectName ? \App\Models\Subject::where('name', $selectedSubjectName)->first() : null;
                $targetCs = ($classId && $subjectModel) ? \App\Models\ClassSubject::where('class_id', $classId)->where('subject_id', $subjectModel->id)->first() : null;

                $attQuery = Attendance::with('details')->where('date', $startDate)->where('class_id', $classId);
                $att = $attQuery->where(function($q) use ($session, $targetCs, $selectedSubjectName) {
                    $q->where('session', $session);
                    if ($targetCs && \Illuminate\Support\Facades\Schema::hasColumn('attendance', 'class_subject_id')) {
                        $q->orWhere('class_subject_id', $targetCs->id);
                    }
                    if ($selectedSubjectName) {
                        $q->orWhere('notes', 'like', "%{$selectedSubjectName}%");
                    }
                })->first();

                $mapelDetails = $att ? $att->details->keyBy('student_id') : collect();
                if ($att) {
                    $sessionNotes = $att->notes ?? '';
                }

                foreach ($students as $student) {
                    $sId = $student->id;
                    $hDet = $homeroomDetails->get($sId);
                    $mDet = $mapelDetails->get($sId);

                    // 1. Jika di Absensi Harian/Kelas dicatat Sakit atau Izin, jadikan status prioritas utama
                    if ($hDet && in_array($hDet->status, ['sakit', 'izin'], true)) {
                        $existing[$sId] = [
                            'status' => $hDet->status,
                            'check_in' => null,
                            'notes' => $hDet->notes ?: 'Dicatat ' . ucfirst($hDet->status) . ' oleh Wali Kelas',
                            'from_homeroom' => true,
                        ];
                    }
                    // 2. Jika sudah pernah diabsen di sesi mapel ini
                    elseif ($mDet) {
                        $existing[$sId] = [
                            'status' => $mDet->status,
                            'check_in' => $mDet->check_in ? $mDet->check_in->format('H:i') : null,
                            'notes' => $mDet->notes,
                            'from_homeroom' => false,
                        ];
                    }
                    // 3. Jika belum ada di mapel ini tapi ada di absensi harian kelas
                    elseif ($hDet) {
                        $existing[$sId] = [
                            'status' => $hDet->status,
                            'check_in' => $hDet->check_in ? $hDet->check_in->format('H:i') : null,
                            'notes' => $hDet->notes,
                            'from_homeroom' => false,
                        ];
                    }
                }
            }
        }

        $dayName = Carbon::parse($startDate)->translatedFormat('l');
        $fullDateFormatted = Carbon::parse($startDate)->translatedFormat('l, d F Y');

        // Clean up orphaned Schedules (where class_id is null or class has been deleted)
        Schedule::whereNull('class_id')->orWhereDoesntHave('class')->delete();

        $mySchedules = collect();
        if ($isGuruOnly && $teacherObj) {
            $mySchedules = Schedule::whereHas('class')
                ->with('class')
                ->where('teacher_id', $teacherObj->id)
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get();
        } else {
            $mySchedules = Schedule::whereHas('class')
                ->with(['class', 'teacher'])
                ->when($classId, fn($q) => $q->where('class_id', $classId))
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get();
        }

        return view('attendance.create', compact('classes', 'students', 'date', 'startDate', 'endDate', 'classId', 'session', 'existing', 'isRangeMode', 'rangeRecaps', 'sessionNotes', 'period', 'sessionLabels', 'selectedSubjectName', 'selectedSubjectInfo', 'hasSaturday', 'dayName', 'fullDateFormatted', 'mySchedules'));
    }


    public function quickSave(Request $request)
    {
        if (!Setting::isFeatureEnabled('subject_attendance')) {
            return response()->json(['success' => false, 'message' => 'Fitur Presensi Mata Pelajaran sedang dinonaktifkan oleh Superadmin.'], 403);
        }

        $request->validate([
            'date' => 'required|date',
            'class_id' => 'required|exists:classes,id',
            'session' => 'required|string|max:50',
            'student_id' => 'required|exists:students,id',
            'status' => 'required|in:hadir,alfa,sakit,izin,terlambat',
            'notes' => 'nullable|string|max:255',
        ]);

        $date = $request->date;
        $classId = $request->class_id;
        $studentId = $request->student_id;
        $status = $request->status;
        $session = $request->session;
        $notes = $request->get('notes');
        if (in_array($session, ['pagi', 'siang', 'harian'], true)) {
            $session = 'mapel_1';
        }

        // Check if marked Sakit/Izin by Wali Kelas di Absensi Harian/Kelas
        $isHomeroomSickPermission = AttendanceDetail::whereHas('attendance', function ($q) use ($classId, $date) {
            $q->where('class_id', $classId)
              ->where('date', $date)
              ->whereIn('session', ['pagi', 'siang', 'harian']);
        })->where('student_id', $studentId)
          ->whereIn('status', ['sakit', 'izin'])
          ->exists();

        if ($isHomeroomSickPermission) {
            return response()->json([
                'success' => false,
                'message' => 'Status siswa ini sudah dicatat Sakit/Izin oleh Wali Kelas dan tidak dapat diubah.',
            ], 422);
        }

        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            $classObj = Classes::with('homeroomTeacher')->find($classId);
            $teacher = $classObj?->homeroomTeacher ?? Teacher::active()->first();
        }

        $this->service->storeAttendance([
            'date' => $date,
            'class_id' => $classId,
            'session' => $session,
            'details' => [
                $studentId => [
                    'status' => $status,
                    'check_in' => $status === 'hadir' ? now()->format('H:i') : null,
                    'notes' => $notes,
                ]
            ]
        ], $teacher);

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil disimpan!',
        ]);
    }

    public function saveSessionNotes(Request $request)
    {
        if (!Setting::isFeatureEnabled('subject_attendance')) {
            return response()->json(['success' => false, 'message' => 'Fitur Presensi Mata Pelajaran sedang dinonaktifkan oleh Superadmin.'], 403);
        }

        $request->validate([
            'date' => 'required|date',
            'class_id' => 'required|exists:classes,id',
            'session' => 'required|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        $session = $request->session;
        if (in_array($session, ['pagi', 'siang', 'harian'], true)) {
            $session = 'mapel_1';
        }

        $teacher = auth()->user()->teacher;

        $att = Attendance::updateOrCreate(
            [
                'date' => $request->date,
                'class_id' => $request->class_id,
                'session' => $session,
            ],
            [
                'teacher_id' => $teacher?->id,
                'notes' => $request->notes,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Catatan laporan sesi mapel berhasil disimpan!',
            'notes' => $att->notes,
        ]);
    }

    public function homeroomCreate(Request $request)
    {
        $user = auth()->user();
        if ($user->isGuru()) {
            $teacher = $user->teacher;
            $classes = $teacher 
                ? $teacher->homeroomClasses()->orderBy('name')->get(['id', 'name', 'grade']) 
                : collect();

            if ($classes->isEmpty()) {
                return redirect()->route('attendance.create')
                    ->with('error', 'Anda bukan Wali Kelas dari kelas mana pun.');
            }
        } else {
            $classes = Classes::orderBy('name')->get(['id', 'name', 'grade']);
        }

        $date = $request->get('date', today()->toDateString());
        $classId = $request->get('class_id', $classes->first()?->id);
        $selectedClass = $classId ? Classes::find($classId) : null;
        $session = $request->get('session', $selectedClass?->session ?? 'pagi');

        $students = $classId
            ? Student::forClass($classId)->active()->orderBy('name')->get()
            : collect();

        $existing = [];
        if ($classId && $date) {
            $att = Attendance::where([
                'date' => $date,
                'class_id' => $classId,
                'session' => $session,
            ])->first();

            if ($att) {
                foreach ($att->details as $detail) {
                    $existing[$detail->student_id] = [
                        'status' => $detail->status,
                        'check_in' => $detail->check_in ? $detail->check_in->format('H:i') : null,
                        'notes' => $detail->notes,
                    ];
                }
            }
        }

        return view('attendance.homeroom-create', compact('classes', 'students', 'date', 'classId', 'session', 'existing'));
    }

    public function homeroomStore(StoreAttendanceRequest $request)
    {
        try {
            $this->service->storeAttendance($request->validated(), Auth::user()->teacher);
            
            return redirect()->route('attendance.index', ['class_id' => $request->class_id])
                ->with('success', 'Absensi harian kelas berhasil disimpan!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan absensi kelas: ' . $e->getMessage());
        }
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
        $user = auth()->user();
        if ($user->isGuru()) {
            $teacher = $user->teacher;
            $canView = $teacher && (
                $teacher->homeroomClasses()->where('id', $attendance->class_id)->exists() ||
                $teacher->classSubjects()->where('class_id', $attendance->class_id)->exists() ||
                $teacher->schedules()->where('class_id', $attendance->class_id)->exists() ||
                $attendance->teacher_id == $teacher->id
            );
            if (!$canView) {
                abort(403, 'Anda tidak memiliki akses ke presensi kelas ini.');
            }
        }

        $attendance->load(['class', 'teacher', 'details.student']);

        [$periodStart, $periodEnd] = $this->resolveDateBounds($request);

        // ── Ambil semua sesi yang tersedia di periode ini untuk kelas ini ──
        $filterSession = $request->get('filter_session', 'all');

        $allSessionsInPeriod = Attendance::where('class_id', $attendance->class_id)
            ->whereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->orderBy('session')
            ->pluck('session')
            ->unique()
            ->values();

        // ── Query period attendances sesuai filter sesi ──
        $periodQuery = Attendance::with(['details.student', 'details.attendance.teacher', 'teacher'])
            ->where('class_id', $attendance->class_id)
            ->whereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->orderByDesc('date');

        if ($filterSession === 'all') {
            $periodQuery->whereIn('session', ['pagi', 'siang', 'harian']);
        } elseif ($filterSession === 'all_mapel') {
            $periodQuery->where('session', 'like', 'mapel_%');
        } elseif ($filterSession !== 'all') {
            $periodQuery->where('session', $filterSession);
        }

        $periodAttendances = $periodQuery->get();

        $periodDetails = $periodAttendances->flatMap->details;
        $periodStats = [
            'sessions' => $periodAttendances->count(),
            'hadir' => $periodDetails->where('status', 'hadir')->count(),
            'terlambat' => $periodDetails->where('status', 'terlambat')->count(),
            'alfa' => $periodDetails->where('status', 'alfa')->count(),
            'izin_sakit' => $periodDetails->whereIn('status', ['sakit', 'izin'])->count(),
        ];

        $startOfWeekAnchor = $periodStart->copy()->startOfWeek(Carbon::MONDAY);
        $scheduleCache = [];

        $studentRecaps = Student::forClass($attendance->class_id)->active()->orderBy('name')->get()
            ->map(function ($student) use ($periodDetails, $startOfWeekAnchor, $attendance, &$scheduleCache) {
                $details = $periodDetails->where('student_id', $student->id);
                return [
                    'student' => $student,
                    'hadir' => $details->where('status', 'hadir')->count(),
                    'terlambat' => $details->where('status', 'terlambat')->count(),
                    'izin_sakit' => $details->whereIn('status', ['sakit', 'izin'])->count(),
                    'alfa' => $details->where('status', 'alfa')->count(),
                    'total' => $details->count(),
                    'logs' => $details->map(function (AttendanceDetail $d) use ($startOfWeekAnchor, $attendance, &$scheduleCache) {
                        $cDate = $d->attendance?->date ? Carbon::parse($d->attendance->date) : null;
                        $dayDate = $cDate ? $cDate->translatedFormat('l, d F Y') : '-';
                        $weekNum = $cDate ? ((int) $startOfWeekAnchor->diffInWeeks($cDate->copy()->startOfWeek(Carbon::MONDAY)) + 1) : 1;

                        $sessionKey = $d->attendance?->session ?? 'pagi';
                        $sessWeight = match($sessionKey) {
                            'pagi' => 0,
                            'mapel_1' => 1,
                            'mapel_2' => 2,
                            'mapel_3' => 3,
                            'mapel_4' => 4,
                            'mapel_5' => 5,
                            'mapel_6' => 6,
                            'mapel_7' => 7,
                            'mapel_8' => 8,
                            'siang' => 9,
                            default => 10,
                        };

                        $sessionFormatted = ($cDate && str_starts_with($sessionKey, 'mapel_'))
                            ? $this->resolveSessionSubjectLabel($attendance->class_id, $cDate->toDateString(), $sessionKey, $scheduleCache, $d->attendance)
                            : str_replace('_', ' ', ucfirst($sessionKey));

                        return [
                            'raw_date' => $d->attendance?->date ? $d->attendance->date->toDateString() : '-',
                            'day_date' => $dayDate,
                            'date' => $dayDate,
                            'week_num' => $weekNum,
                            'sess_weight' => $sessWeight,
                            'session' => $sessionFormatted,
                            'status' => $d->status,
                            'status_label' => match($d->status) {
                                'hadir' => '🟢 Hadir',
                                'terlambat' => '⏰ Terlambat',
                                'sakit' => '🤒 Sakit',
                                'izin' => '📄 Izin',
                                'alfa' => '🔴 Alfa (Tanpa Keterangan)',
                                default => ucfirst($d->status)
                            },
                            'check_in' => $d->check_in ? $d->check_in->format('H:i') . ' WIB' : '-',
                            'notes' => $d->notes ?: 'Tidak ada alasan / keterangan tertulis',
                        ];
                    })->values(),
                ];
            })
            ->values();
        $mapelAttendances = Attendance::with(['teacher', 'details.student', 'details.attendance.teacher'])
            ->where('class_id', $attendance->class_id)
            ->where('date', $attendance->date)
            ->where('session', 'like', 'mapel_%')
            ->orderBy('session')
            ->get();

        $mapelSessions = $mapelAttendances->pluck('session')->unique()->values();

        $mapelRecaps = Student::forClass($attendance->class_id)->active()->orderBy('name')->get()
            ->map(function ($student) use ($mapelAttendances, $mapelSessions) {
                $sessionStatuses = [];
                $sessionNotesMap = [];
                foreach ($mapelSessions as $sessKey) {
                    $att = $mapelAttendances->firstWhere('session', $sessKey);
                    $det = $att ? $att->details->firstWhere('student_id', $student->id) : null;
                    $sessionStatuses[$sessKey] = $det ? $det->status : null;
                    $sessionNotesMap[$sessKey] = $det ? $det->notes : null;
                }

                return [
                    'student' => $student,
                    'statuses' => $sessionStatuses,
                    'notes' => $sessionNotesMap,
                ];
            });

        $hasSaturday = $this->hasSaturdaySchedule($attendance->class_id);

        return view('attendance.show', compact(
            'attendance',
            'periodStart',
            'periodEnd',
            'periodAttendances',
            'periodStats',
            'studentRecaps',
            'mapelAttendances',
            'mapelSessions',
            'mapelRecaps',
            'allSessionsInPeriod',
            'filterSession',
            'hasSaturday'
        ));
    }

    public function edit(Attendance $attendance)
    {
        $user = auth()->user();
        if ($user->isGuru()) {
            $teacher = $user->teacher;
            $isHomeroom = $teacher && $teacher->homeroomClasses()->where('id', $attendance->class_id)->exists();
            if (!$isHomeroom) {
                abort(403, 'Unauthorized action.');
            }
        }

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

        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);

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

    private function resolveDateBounds(Request $request): array
    {
        $period = $request->get('period');

        // Period dropdown always takes priority when explicitly set
        if ($period === 'today') {
            $today = today();
            return [$today, $today];
        }

        if ($period === 'week') {
            $hasSaturday = $this->hasSaturdaySchedule($request->get('class_id'));
            $endDay = $hasSaturday ? Carbon::SATURDAY : Carbon::FRIDAY;
            return [today()->startOfWeek(Carbon::MONDAY), today()->endOfWeek($endDay)];
        }

        if ($period === '30_days' || $period === 'last_30_days') {
            return [today()->subDays(30), today()];
        }

        if ($period === 'month') {
            return [today()->startOfMonth(), today()->endOfMonth()];
        }

        // If no period (or period=custom), fall back to manual date inputs
        if ($request->filled('start_date') && $request->filled('end_date')) {
            return [Carbon::parse($request->start_date), Carbon::parse($request->end_date)];
        }

        if ($request->filled('start_date')) {
            $d = Carbon::parse($request->start_date);
            return [$d, $d];
        }

        if ($request->filled('end_date')) {
            $d = Carbon::parse($request->end_date);
            return [$d, $d];
        }

        if ($request->filled('date')) {
            $d = Carbon::parse($request->date);
            return [$d, $d];
        }

        // Default: 30 hari terakhir
        return [today()->subDays(30), today()];
    }

    private function applyAttendanceFilters($query, Request $request)
    {
        [$sDate, $eDate] = $this->resolveDateBounds($request);
        return $query->whereBetween('date', [$sDate->toDateString(), $eDate->toDateString()]);
    }

    private function hasSaturdaySchedule(?int $classId = null): bool
    {
        return Schedule::query()
            ->when($classId, fn($q, $cid) => $q->where('class_id', $cid))
            ->where('day_of_week', 6)
            ->exists();
    }

    private function resolveSessionSubjectLabel(int $classId, string $dateStr, string $sessionKey, array &$scheduleCache, ?Attendance $attendance = null): string
    {
        if (in_array($sessionKey, ['pagi', 'siang', 'harian'], true)) {
            return 'Presensi ' . ucfirst($sessionKey);
        }

        if (!str_starts_with($sessionKey, 'mapel_')) {
            return ucfirst($sessionKey);
        }

        $mapelNum = (int) str_replace('mapel_', '', $sessionKey);
        $dayOfWeek = Carbon::parse($dateStr)->dayOfWeekIso; // 1 (Mon) .. 7 (Sun)
        $cacheKey = "{$classId}_{$dayOfWeek}";

        if (!isset($scheduleCache[$cacheKey])) {
            $scheduleCache[$cacheKey] = Schedule::with('teacher')
                ->where('class_id', $classId)
                ->where('day_of_week', $dayOfWeek)
                ->orderBy('start_time')
                ->get();
        }

        $sched = $scheduleCache[$cacheKey]->get($mapelNum - 1);
        $subjectName = $sched?->subject;
        $teacherName = $sched?->teacher?->name ?: $attendance?->teacher?->name;

        if (!$subjectName) {
            $csCacheKey = "cs_{$classId}";
            if (!isset($scheduleCache[$csCacheKey])) {
                $scheduleCache[$csCacheKey] = ClassSubject::with(['subject', 'teacher'])
                    ->where('class_id', $classId)
                    ->get();
            }
            $cs = $scheduleCache[$csCacheKey]->get($mapelNum - 1);
            $subjectName = $cs?->subject?->name;
            $teacherName = $teacherName ?: $cs?->teacher?->name;
        }

        if ($subjectName) {
            $teacherStr = $teacherName ? " — Guru: {$teacherName}" : '';
            return "Jam Mapel {$mapelNum}: {$subjectName}{$teacherStr}";
        }

        if ($teacherName) {
            return "Jam Mapel {$mapelNum} — Guru: {$teacherName}";
        }

        return "Jam Mapel {$mapelNum}";
    }
}

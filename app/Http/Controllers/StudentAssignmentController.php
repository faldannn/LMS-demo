<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitAssignmentRequest;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassSubject;
use App\Services\AssignmentService;
use Illuminate\Http\Request;

class StudentAssignmentController extends Controller
{
    public function __construct(private readonly AssignmentService $assignmentService) {}

    public function index()
    {
        $student = auth()->user()->student;
        
        if (!$student || !$student->class_id) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak terdaftar dalam kelas apapun.');
        }

        $classSubjects = ClassSubject::with(['subject', 'teacher.subjects', 'assignments' => function ($query) {
            $query->where('is_published', true)->orderBy('due_date', 'asc');
        }, 'assignments.submissions' => function ($query) use ($student) {
            $query->where('student_id', $student->id);
        }])
        ->where('class_id', $student->class_id)
        ->get();

        return view('student-assignments.index', compact('classSubjects'));
    }

    public function show(Assignment $assignment)
    {
        $this->authorize('view', $assignment);

        if (!$assignment->is_published) {
            abort(404);
        }

        $student = auth()->user()->student;
        $submission = $assignment->submissions()->where('student_id', $student->id)->with('grade')->first();
        
        return view('student-assignments.show', compact('assignment', 'submission'));
    }

    public function submit(SubmitAssignmentRequest $request, Assignment $assignment)
    {
        $this->authorize('submit', $assignment);

        if (!$assignment->is_published) {
            abort(404);
        }

        $student = auth()->user()->student;
        $submission = $assignment->submissions()->where('student_id', $student->id)->first();
        
        if ($submission && $submission->grade) {
            return redirect()->back()->with('error', 'Tugas sudah dinilai, tidak dapat diubah lagi.');
        }

        $data = $request->validated();
        $data['assignment_id'] = $assignment->id;
        $data['student_id'] = $student->id;
        $data['submitted_at'] = now();
        $data['is_late'] = $this->assignmentService->checkIsLate($assignment);

        if ($request->hasFile('file')) {
            if ($submission && $submission->file_path) {
                $this->assignmentService->deleteFile($submission->file_path);
            }
            $fileData = $this->assignmentService->uploadFile($request->file('file'), 'submissions');
            $data = array_merge($data, $fileData);
        }

        unset($data['file']);
        if ($submission) {
            $submission->update($data);
            $message = 'Tugas berhasil diperbarui.';
        } else {
            AssignmentSubmission::create($data);
            $message = 'Tugas berhasil dikumpulkan.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function showClassSubject(ClassSubject $classSubject)
    {
        $user = auth()->user();
        $student = $user->student;

        if ($user->isSiswa() && (!$student || $student->class_id !== $classSubject->class_id)) {
            abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini.');
        }

        $classSubject->load([
            'subject',
            'teacher.subjects',
            'materials' => function ($query) {
                $query->where('is_published', true)->orderBy('order')->orderBy('created_at', 'desc');
            },
            'assignments' => function ($query) {
                $query->where('is_published', true)->orderBy('due_date', 'asc');
            },
            'assignments.submissions' => function ($query) use ($student) {
                if ($student) {
                    $query->where('student_id', $student->id);
                }
            }
        ]);

        $dayOfWeek = today()->dayOfWeekIso;
        $daySchedules = \App\Models\Schedule::where('class_id', $classSubject->class_id)
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('start_time')
            ->get();

        $slotNum = null;
        if ($daySchedules->isNotEmpty()) {
            foreach ($daySchedules as $idx => $sched) {
                if ($sched->subject === $classSubject->subject->name || stripos($sched->subject, $classSubject->subject->name) !== false) {
                    $slotNum = $idx + 1;
                    break;
                }
            }
        }

        $sessionKey = $slotNum ? "mapel_{$slotNum}" : 'mapel_1';
        $subName = $classSubject->subject->name;

        // Ambil atau inisialisasi presensi mapel hari ini
        $todayAttendance = \App\Models\Attendance::with(['details.student'])
            ->where('class_id', $classSubject->class_id)
            ->whereDate('date', today())
            ->where(function ($q) use ($sessionKey, $subName, $classSubject) {
                if ($sessionKey) {
                    $q->where('session', $sessionKey);
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('attendance', 'class_subject_id')) {
                    $q->orWhere('class_subject_id', $classSubject->id);
                }
                $q->orWhere('notes', 'like', "%{$subName}%");
            })
            ->first();

        // Jadwal hari ini untuk mapel ini
        $activeSchedule = $daySchedules->first(function($s) use ($classSubject) {
            return $s->subject === $classSubject->subject->name || stripos($s->subject, $classSubject->subject->name) !== false;
        });

        // Ambil semua siswa di kelas ini
        $classStudents = \App\Models\Student::where('class_id', $classSubject->class_id)
            ->orderBy('name')
            ->get();

        // Jika hari ini ada jadwal dan belum dibuat presensinya, buat otomatis agar daftar siswa siap
        if (!$todayAttendance && $activeSchedule) {
            $attConditions = [
                'date' => today()->toDateString(),
                'class_id' => $classSubject->class_id,
                'session' => $sessionKey,
            ];
            if (\Illuminate\Support\Facades\Schema::hasColumn('attendance', 'class_subject_id')) {
                $attConditions['class_subject_id'] = $classSubject->id;
            }

            $todayAttendance = \App\Models\Attendance::firstOrCreate($attConditions, [
                'teacher_id' => $classSubject->teacher_id,
                'notes' => 'Presensi Mapel ' . $classSubject->subject->name,
            ]);
        }

        // Sinkronisasi seluruh murid ke attendance_details jika attendance aktif
        if ($todayAttendance && $classStudents->isNotEmpty()) {
            $existingDetailStudentIds = $todayAttendance->details->pluck('student_id')->toArray();
            $missingStudents = $classStudents->whereNotIn('id', $existingDetailStudentIds);

            if ($missingStudents->isNotEmpty()) {
                // Cek apakah ada catatan izin/sakit wali kelas dari absensi harian
                $homeroomAtt = \App\Models\Attendance::with('details')
                    ->where('class_id', $classSubject->class_id)
                    ->whereDate('date', today())
                    ->whereIn('session', ['pagi', 'siang', 'harian'])
                    ->first();
                $homeroomMap = $homeroomAtt ? $homeroomAtt->details->keyBy('student_id') : collect();

                $toInsert = [];
                foreach ($missingStudents as $mStudent) {
                    $defStatus = 'alfa';
                    if ($homeroomMap->has($mStudent->id)) {
                        $hStatus = $homeroomMap[$mStudent->id]->status;
                        if (in_array($hStatus, ['sakit', 'izin'], true)) {
                            $defStatus = $hStatus;
                        }
                    }
                    $toInsert[] = [
                        'attendance_id' => $todayAttendance->id,
                        'student_id' => $mStudent->id,
                        'status' => $defStatus,
                    ];
                }
                if (!empty($toInsert)) {
                    \App\Models\AttendanceDetail::insert($toInsert);
                    $todayAttendance->load('details.student');
                }
            }
        }

        $studentAttendanceDetail = null;
        if ($todayAttendance && $student) {
            $studentAttendanceDetail = $todayAttendance->details->firstWhere('student_id', $student->id);
        }

        // Fallback jika belum terisi di mapel tapi ada catatan sakit/izin dari absensi wali kelas
        if ($student && (!$studentAttendanceDetail || $studentAttendanceDetail->status === 'alfa')) {
            $homeroomAtt = \App\Models\Attendance::with('details')
                ->where('class_id', $classSubject->class_id)
                ->whereDate('date', today())
                ->whereIn('session', ['pagi', 'siang', 'harian'])
                ->first();

            if ($homeroomAtt) {
                $hDet = $homeroomAtt->details->firstWhere('student_id', $student->id);
                if ($hDet && in_array($hDet->status, ['sakit', 'izin'], true)) {
                    if ($studentAttendanceDetail) {
                        $studentAttendanceDetail->status = $hDet->status;
                        $studentAttendanceDetail->notes = 'Catatan Wali Kelas';
                        $studentAttendanceDetail->save();
                    }
                }
            }
        }

        $attendanceWindow = $this->resolveAttendanceWindow($classSubject);

        // Statistik Kehadiran Kelas
        $attendanceStats = [
            'total' => $classStudents->count(),
            'hadir' => 0,
            'terlambat' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alfa' => 0,
        ];

        if ($todayAttendance) {
            foreach ($todayAttendance->details as $det) {
                if (isset($attendanceStats[$det->status])) {
                    $attendanceStats[$det->status]++;
                }
            }
        }

        return view('student-class-subjects.show', compact(
            'classSubject',
            'todayAttendance',
            'studentAttendanceDetail',
            'attendanceWindow',
            'classStudents',
            'attendanceStats',
            'activeSchedule'
        ));
    }

    public function checkInSubject(ClassSubject $classSubject)
    {
        if (!\App\Models\Setting::isFeatureEnabled('subject_attendance')) {
            return back()->withErrors(['attendance' => 'Fitur Presensi Mata Pelajaran sedang dinonaktifkan oleh Superadmin.']);
        }

        $student = auth()->user()->student;

        if (!$student || $student->class_id !== $classSubject->class_id) {
            return back()->withErrors(['attendance' => 'Anda tidak memiliki akses untuk melakukan absensi pada mata pelajaran ini.']);
        }

        $attendanceWindow = $this->resolveAttendanceWindow($classSubject);
        if (!$attendanceWindow['can_check_in']) {
            return back()->withErrors(['attendance' => $attendanceWindow['message']]);
        }

        $isLate = !empty($attendanceWindow['is_late']);
        $newStatus = $isLate ? 'terlambat' : 'hadir';

        $dayOfWeek = today()->dayOfWeekIso;
        $daySchedules = \App\Models\Schedule::where('class_id', $classSubject->class_id)
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('start_time')
            ->get();

        $sessionKey = 'mapel_1';
        if ($daySchedules->isNotEmpty()) {
            foreach ($daySchedules as $idx => $sched) {
                if (stripos($sched->subject, $classSubject->subject->name) !== false) {
                    $slotNum = $idx + 1;
                    $sessionKey = "mapel_{$slotNum}";
                    break;
                }
            }
        }

        $attendanceConditions = [
            'date' => today()->toDateString(),
            'class_id' => $classSubject->class_id,
            'session' => $sessionKey,
        ];
        if (\Illuminate\Support\Facades\Schema::hasColumn('attendance', 'class_subject_id')) {
            $attendanceConditions['class_subject_id'] = $classSubject->id;
        }

        $attendance = \App\Models\Attendance::firstOrCreate($attendanceConditions, [
            'teacher_id' => $classSubject->teacher_id,
            'notes' => 'Presensi Mandiri Siswa - Mapel ' . $classSubject->subject->name,
        ]);

        \App\Models\AttendanceDetail::updateOrCreate([
            'attendance_id' => $attendance->id,
            'student_id' => $student->id,
        ], [
            'status' => $newStatus,
            'check_in' => now()->format('H:i:s'),
            'notes' => $isLate ? 'Presensi mandiri (terlambat)' : 'Presensi mandiri hadir',
        ]);

        $statusMsg = $isLate ? 'Hadir (Terlambat)' : 'Hadir';
        return back()->with('success', 'Berhasil! Kehadiran Anda untuk mata pelajaran ' . $classSubject->subject->name . ' telah dicatat sebagai ' . $statusMsg . '.');
    }

    private function resolveAttendanceWindow(ClassSubject $classSubject): array
    {
        $dayOfWeek = today()->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
        $todaySchedule = \App\Models\Schedule::where('class_id', $classSubject->class_id)
            ->where('day_of_week', $dayOfWeek)
            ->where(function($q) use ($classSubject) {
                $q->where('subject', $classSubject->subject->name)
                  ->orWhere('subject', 'like', "%{$classSubject->subject->name}%");
            })
            ->first();

        if ($todaySchedule) {
            $now = now();
            $startTime = \Carbon\Carbon::parse(today()->toDateString() . ' ' . $todaySchedule->start_time->format('H:i:s'));
            $endTime = \Carbon\Carbon::parse(today()->toDateString() . ' ' . $todaySchedule->end_time->format('H:i:s'));

            if ($now->lt($startTime)) {
                return [
                    'status' => 'not_started',
                    'can_check_in' => false,
                    'start_time' => $startTime->format('H:i'),
                    'end_time' => $endTime->format('H:i'),
                    'message' => 'Absensi mata pelajaran ini belum dibuka hari ini. Jam presensi: ' . $startTime->format('H:i') . ' - ' . $endTime->format('H:i') . ' WIB.',
                ];
            } elseif ($now->gt($endTime)) {
                return [
                    'status' => 'ended',
                    'can_check_in' => true,
                    'is_late' => true,
                    'start_time' => $startTime->format('H:i'),
                    'end_time' => $endTime->format('H:i'),
                    'message' => 'Sesi reguler telah berakhir pada pukul ' . $endTime->format('H:i') . ' WIB. Anda masih dapat melakukan presensi mandiri (tercatat Terlambat).',
                ];
            } else {
                return [
                    'status' => 'open',
                    'can_check_in' => true,
                    'start_time' => $startTime->format('H:i'),
                    'end_time' => $endTime->format('H:i'),
                    'message' => 'Sesi presensi sedang berlangsung hingga pukul ' . $endTime->format('H:i') . ' WIB.',
                ];
            }
        }

        $anySchedule = \App\Models\Schedule::where('class_id', $classSubject->class_id)
            ->where(function($q) use ($classSubject) {
                $q->where('subject', $classSubject->subject->name)
                  ->orWhere('subject', 'like', "%{$classSubject->subject->name}%");
            })
            ->orderBy('day_of_week')
            ->first();

        if ($anySchedule) {
            $dayName = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][$anySchedule->day_of_week] ?? '';
            $timeStr = $anySchedule->start_time ? $anySchedule->start_time->format('H:i') : '';
            return [
                'status' => 'no_schedule_today',
                'can_check_in' => false,
                'next_day' => $dayName,
                'start_time' => $timeStr,
                'message' => 'Hari ini tidak ada jadwal mata pelajaran ' . $classSubject->subject->name . '. Jadwal berikutnya: Hari ' . $dayName . ($timeStr ? ' pukul ' . $timeStr . ' WIB' : '') . '.',
            ];
        }

        return [
            'status' => 'open',
            'can_check_in' => true,
            'start_time' => '07:15',
            'end_time' => '17:00',
            'message' => 'Sesi presensi mata pelajaran terbuka.',
        ];
    }


}


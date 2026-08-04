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
        $student = auth()->user()->student;

        if (!$student || $student->class_id !== $classSubject->class_id) {
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
                $query->where('student_id', $student->id);
            }
        ]);

        $todayAttendance = \App\Models\Attendance::query()
            ->where('class_id', $classSubject->class_id)
            ->when(\Illuminate\Support\Facades\Schema::hasColumn('attendance', 'class_subject_id'), function ($q) use ($classSubject) {
                $q->where('class_subject_id', $classSubject->id);
            })
            ->whereDate('date', today())
            ->first();

        $studentAttendanceDetail = null;
        if ($todayAttendance) {
            $studentAttendanceDetail = \App\Models\AttendanceDetail::query()
                ->where('attendance_id', $todayAttendance->id)
                ->where('student_id', $student->id)
                ->first();
        }

        $attendanceWindow = $this->resolveAttendanceWindow($classSubject);

        return view('student-class-subjects.show', compact('classSubject', 'todayAttendance', 'studentAttendanceDetail', 'attendanceWindow'));
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
            'date' => today(),
            'class_id' => $classSubject->class_id,
            'session' => $sessionKey,
        ];
        if (\Illuminate\Support\Facades\Schema::hasColumn('attendance', 'class_subject_id')) {
            $attendanceConditions['class_subject_id'] = $classSubject->id;
        }

        $attendance = \App\Models\Attendance::firstOrCreate($attendanceConditions, [
            'teacher_id' => $classSubject->teacher_id,
            'notes' => 'Absensi Mandiri Siswa - Mapel ' . $classSubject->subject->name,
        ]);

        $allStudents = \App\Models\Student::where('class_id', $classSubject->class_id)->get();
        foreach ($allStudents as $s) {
            \App\Models\AttendanceDetail::firstOrCreate([
                'attendance_id' => $attendance->id,
                'student_id' => $s->id,
            ], [
                'status' => 'alfa',
            ]);
        }

        $detail = \App\Models\AttendanceDetail::where('attendance_id', $attendance->id)
            ->where('student_id', $student->id)
            ->first();

        if ($detail) {
            $detail->update([
                'status' => 'hadir',
                'check_in' => now(),
            ]);
        }

        return back()->with('success', 'Berhasil! Kehadiran Anda untuk mata pelajaran ' . $classSubject->subject->name . ' telah dicatat.');
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
                    'can_check_in' => false,
                    'start_time' => $startTime->format('H:i'),
                    'end_time' => $endTime->format('H:i'),
                    'message' => 'Sesi presensi untuk mata pelajaran ini telah berakhir pada pukul ' . $endTime->format('H:i') . ' WIB.',
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


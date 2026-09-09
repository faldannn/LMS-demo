<?php

namespace App\Http\Controllers;

use App\Models\{AcademicYear, Classes, ClassSubject, Schedule, Setting, Teacher};
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    private function getOperationalDays(): array
    {
        $mode = Setting::get('school_operational_days', '5_days');
        $days = [
            '1' => 'Senin',
            '2' => 'Selasa',
            '3' => 'Rabu',
            '4' => 'Kamis',
            '5' => 'Jumat',
        ];
        if ($mode === '6_days') {
            $days['6'] = 'Sabtu';
        }
        return $days;
    }

    private function getMaxDayOfWeek(): int
    {
        return Setting::get('school_operational_days', '5_days') === '6_days' ? 6 : 5;
    }

    /**
     * Synchronize ClassSubject (E-Learning Portal mappings) with active Schedule items for a class.
     * Ensures adding, editing, or deleting schedules automatically syncs the E-Learning Portal!
     */
    private function syncClassSubjects(?int $classId): void
    {
        if (!$classId) {
            return;
        }

        $activeYear = AcademicYear::active();
        if (!$activeYear) {
            return;
        }

        $schedules = Schedule::where('class_id', $classId)->get();
        $validSubjectIds = [];

        foreach ($schedules as $sched) {
            if (empty($sched->subject)) {
                continue;
            }

            $subjectModel = \App\Models\Subject::where('name', $sched->subject)->first();
            if (!$subjectModel) {
                $subjectModel = \App\Models\Subject::where('name', 'like', "%{$sched->subject}%")->first();
            }

            if (!$subjectModel) {
                $code = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $sched->subject), 0, 8));
                if (empty($code)) {
                    $code = 'MAPEL' . rand(100, 999);
                }
                $subjectModel = \App\Models\Subject::create([
                    'code'   => $code,
                    'name'   => trim($sched->subject),
                    'status' => true,
                ]);
            }

            if ($subjectModel) {
                $validSubjectIds[] = $subjectModel->id;

                ClassSubject::updateOrCreate([
                    'class_id'         => $classId,
                    'subject_id'       => $subjectModel->id,
                    'academic_year_id' => $activeYear->id,
                ], [
                    'teacher_id'       => $sched->teacher_id,
                ]);

                if ($sched->teacher_id) {
                    $teacher = Teacher::find($sched->teacher_id);
                    if ($teacher) {
                        $teacher->subjects()->syncWithoutDetaching([$subjectModel->id]);
                    }
                }
            }
        }

        $validSubjectIds = array_unique($validSubjectIds);

        // Remove any ClassSubject records for this class that no longer have ANY schedules
        if (empty($validSubjectIds)) {
            ClassSubject::where('class_id', $classId)->delete();
        } else {
            ClassSubject::where('class_id', $classId)
                ->whereNotIn('subject_id', $validSubjectIds)
                ->delete();
        }
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->isSiswa() && $user->student) {
            $classId = $user->student->class_id;
            $classes = collect([$user->student->class]);
        } elseif ($user->isGuru()) {
            $teacher = $user->teacher;
            if ($teacher) {
                $classIds = ClassSubject::where('teacher_id', $teacher->id)
                    ->pluck('class_id')
                    ->unique();
                $classes = Classes::whereIn('id', $classIds)
                    ->orderBy('grade')
                    ->orderBy('name')
                    ->get();
            } else {
                $classes = collect();
            }
            $classId = $request->get('class_id', $classes->first()?->id);
            if ($classId && !$classes->pluck('id')->contains($classId)) {
                $classId = $classes->first()?->id;
            }
        } else {
            $classId = $request->get('class_id', Classes::first()?->id);
            $classes = Classes::orderBy('grade')->orderBy('name')->get();
        }

        $class = Classes::find($classId);

        $operationalDays = $this->getOperationalDays();
        $days = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $maxDay = $this->getMaxDayOfWeek();

        $schedulesList = Schedule::with('teacher')
            ->where('class_id', $classId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        // Old curriculum assignments can reference a removed/soft-deleted subject.
        // Keep the timetable visible, without linking those rows to a missing subject.
        $classSubjects = ClassSubject::where('class_id', $classId)
            ->whereHas('subject')->with('subject')->get();
        
        foreach ($schedulesList as $sched) {
            $matchedCs = $classSubjects->first(function ($cs) use ($sched) {
                return $cs->subject !== null && (int) $cs->teacher_id === (int) $sched->teacher_id && 
                       stripos($cs->subject->name, $sched->subject) !== false;
            });
            
            if (!$matchedCs) {
                $matchedCs = $classSubjects->first(function ($cs) use ($sched) {
                    return $cs->subject !== null && stripos($cs->subject->name, $sched->subject) !== false;
                });
            }

            $sched->class_subject_id = $matchedCs ? $matchedCs->id : null;
        }

        if ($user->isSiswa() && $user->student) {
            $student = $user->student;
            $todayIso = today()->dayOfWeekIso;
            $todayAttendances = \App\Models\Attendance::with('details')
                ->where('class_id', $classId)
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

            $todaySlotScheds = $schedulesList->where('day_of_week', $todayIso)->values();
            foreach ($todaySlotScheds as $idx => $sched) {
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

        $schedules = $schedulesList->groupBy('day_of_week');

        return view('schedules.index', compact('classes', 'class', 'schedules', 'days', 'classId', 'operationalDays', 'maxDay'));
    }

    public function batchCreate(Request $request)
    {
        $classes = Classes::orderBy('grade')->orderBy('name')->get();
        $classId = $request->get('class_id', $classes->first()?->id);
        $class = Classes::find($classId);

        $teachers = Teacher::active()->with('subjects')->orderBy('name')->get()->map(function ($t) {
            return [
                'id' => $t->id,
                'name' => $t->name,
                'taught_subjects' => $t->taught_subject_names,
            ];
        });
        $subjects = \App\Models\Subject::active()->orderBy('name')->get();
        $operationalDays = $this->getOperationalDays();
        $maxDay = $this->getMaxDayOfWeek();

        $existingSchedules = Schedule::where('class_id', $classId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        return view('schedules.batch', compact(
            'classes',
            'class',
            'classId',
            'teachers',
            'subjects',
            'operationalDays',
            'maxDay',
            'existingSchedules'
        ));
    }

    public function batchStore(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'items'    => 'nullable|array',
        ]);

        $classId = (int) $request->class_id;
        $activeYear = AcademicYear::active();

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $classId, $activeYear) {
            Schedule::where('class_id', $classId)->delete();

            $items = $request->input('items', []);
            foreach ($items as $item) {
                if (empty($item['subject']) || empty($item['teacher_id']) || empty($item['start_time']) || empty($item['end_time'])) {
                    continue;
                }

                Schedule::create([
                    'class_id'         => $classId,
                    'teacher_id'       => $item['teacher_id'],
                    'subject'          => $item['subject'],
                    'day_of_week'      => (int) $item['day_of_week'],
                    'start_time'      => $item['start_time'],
                    'end_time'        => $item['end_time'],
                    'room'            => $item['room'] ?? null,
                    'academic_year_id' => $activeYear?->id,
                ]);
            }
        });

        // Sync E-Learning portal ClassSubject mappings
        $this->syncClassSubjects($classId);

        $class = Classes::find($classId);
        return redirect()->route('schedules.index', ['class_id' => $classId])
            ->with('success', "Jadwal mingguan untuk Kelas {$class?->name} berhasil disimpan & disinkronkan ke Portal E-Learning!");
    }

    public function copySchedule(Request $request)
    {
        $request->validate([
            'source_class_id' => 'required|exists:classes,id',
            'target_class_id' => 'required|exists:classes,id|different:source_class_id',
        ]);

        $sourceClassId = (int) $request->source_class_id;
        $targetClassId = (int) $request->target_class_id;
        $sourceSchedules = Schedule::where('class_id', $sourceClassId)->get();
        $targetClass = Classes::find($targetClassId);
        $activeYear = AcademicYear::active();

        if ($sourceSchedules->isEmpty()) {
            return back()->withErrors(['source_class_id' => 'Kelas asal tidak memiliki jadwal untuk disalin.']);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($sourceSchedules, $targetClassId, $activeYear) {
            Schedule::where('class_id', $targetClassId)->delete();

            foreach ($sourceSchedules as $sched) {
                Schedule::create([
                    'class_id'         => $targetClassId,
                    'teacher_id'       => $sched->teacher_id,
                    'subject'          => $sched->subject,
                    'day_of_week'      => $sched->day_of_week,
                    'start_time'      => $sched->start_time ? \Carbon\Carbon::parse($sched->start_time)->format('H:i') : '07:15',
                    'end_time'        => $sched->end_time ? \Carbon\Carbon::parse($sched->end_time)->format('H:i') : '08:00',
                    'room'            => $sched->room,
                    'academic_year_id' => $activeYear?->id,
                ]);
            }
        });

        // Sync E-Learning portal ClassSubject mappings for target class
        $this->syncClassSubjects($targetClassId);

        return redirect()->route('schedules.index', ['class_id' => $targetClassId])
            ->with('success', "Jadwal berhasil disalin ke Kelas {$targetClass?->name} & disinkronkan ke Portal E-Learning!");
    }

    public function create()
    {
        $classes  = Classes::orderBy('name')->get();
        $teachers = Teacher::active()->with('subjects')->orderBy('name')->get();
        $subjects = \App\Models\Subject::orderBy('name')->get();
        $days     = $this->getOperationalDays();
        $maxDay   = $this->getMaxDayOfWeek();
        return view('schedules.create', compact('classes', 'teachers', 'subjects', 'days', 'maxDay'));
    }

    public function store(Request $request)
    {
        $maxDay = $this->getMaxDayOfWeek();
        $data = $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'subject'    => 'required|string|max:100',
            'day_of_week'=> "required|integer|between:1,{$maxDay}",
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'room'       => 'nullable|string|max:20',
        ]);
        
        $activeYear = AcademicYear::active();
        $data['academic_year_id'] = $activeYear?->id;
        
        $schedule = Schedule::create($data);

        // Sync E-Learning portal ClassSubject mappings
        $this->syncClassSubjects((int) $data['class_id']);

        return redirect()->route('schedules.index', ['class_id' => $data['class_id']])
            ->with('success', 'Jadwal berhasil ditambahkan & disinkronkan ke Portal E-Learning.');
    }

    public function edit(Schedule $schedule)
    {
        $classes  = Classes::orderBy('name')->get();
        $teachers = Teacher::active()->with('subjects')->orderBy('name')->get();
        $subjects = \App\Models\Subject::orderBy('name')->get();
        $days     = $this->getOperationalDays();
        $maxDay   = $this->getMaxDayOfWeek();
        
        if ($schedule->start_time) {
            $schedule->start_time_formatted = \Carbon\Carbon::parse($schedule->start_time)->format('H:i');
        }
        if ($schedule->end_time) {
            $schedule->end_time_formatted = \Carbon\Carbon::parse($schedule->end_time)->format('H:i');
        }

        return view('schedules.edit', compact('schedule', 'classes', 'teachers', 'subjects', 'days', 'maxDay'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $oldClassId = (int) $schedule->class_id;
        $maxDay = $this->getMaxDayOfWeek();
        $data = $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'subject'    => 'required|string|max:100',
            'day_of_week'=> "required|integer|between:1,{$maxDay}",
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'room'       => 'nullable|string|max:20',
        ]);
        
        $schedule->update($data);

        // Sync E-Learning portal ClassSubject mappings for current and old class if changed
        $newClassId = (int) $data['class_id'];
        $this->syncClassSubjects($newClassId);
        if ($oldClassId !== $newClassId) {
            $this->syncClassSubjects($oldClassId);
        }

        return redirect()->route('schedules.index', ['class_id' => $newClassId])
            ->with('success', 'Jadwal berhasil diperbarui & disinkronkan ke Portal E-Learning.');
    }

    public function destroy(Schedule $schedule)
    {
        $classId = (int) $schedule->class_id;
        $schedule->delete();

        // Sync E-Learning portal ClassSubject mappings
        $this->syncClassSubjects($classId);

        return redirect()->route('schedules.index', ['class_id' => $classId])
            ->with('success', 'Jadwal berhasil dihapus & disinkronkan ke Portal E-Learning.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'class_id'  => 'required|exists:classes,id',
            'ids'       => 'nullable|array',
            'ids.*'     => 'exists:schedules,id',
            'clear_all' => 'nullable|boolean',
        ]);

        $classId = (int) $request->class_id;
        $class = Classes::find($classId);

        if ($request->boolean('clear_all')) {
            $deletedCount = Schedule::where('class_id', $classId)->delete();
            $this->syncClassSubjects($classId);

            return redirect()->route('schedules.index', ['class_id' => $classId])
                ->with('success', "Seluruh jadwal ({$deletedCount} data) untuk Kelas {$class?->name} berhasil dihapus & Portal E-Learning disinkronkan.");
        }

        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            $deletedCount = Schedule::whereIn('id', $ids)->delete();
            $this->syncClassSubjects($classId);

            return redirect()->route('schedules.index', ['class_id' => $classId])
                ->with('success', "Sebanyak {$deletedCount} jadwal terpilih berhasil dihapus & Portal E-Learning disinkronkan.");
        }

        return redirect()->route('schedules.index', ['class_id' => $classId])
            ->with('error', 'Tidak ada jadwal yang dipilih untuk dihapus.');
    }
}

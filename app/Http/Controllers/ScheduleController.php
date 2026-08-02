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
        // Days 1=Senin … 6=Sabtu — full list for display; filter by setting
        $operationalDays = $this->getOperationalDays();
        $days = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $maxDay = $this->getMaxDayOfWeek();

        $schedulesList = Schedule::with('teacher')
            ->where('class_id', $classId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $classSubjects = ClassSubject::where('class_id', $classId)->with('subject')->get();
        
        foreach ($schedulesList as $sched) {
            $matchedCs = $classSubjects->first(function ($cs) use ($sched) {
                return (int) $cs->teacher_id === (int) $sched->teacher_id && 
                       stripos($cs->subject->name, $sched->subject) !== false;
            });
            
            if (!$matchedCs) {
                $matchedCs = $classSubjects->first(function ($cs) use ($sched) {
                    return stripos($cs->subject->name, $sched->subject) !== false;
                });
            }

            // Auto-create missing ClassSubject links so schedules immediately become clickable/functional
            if (!$matchedCs && $classId) {
                $subjectModel = \App\Models\Subject::where('name', 'like', "%{$sched->subject}%")->first();
                $activeYear = AcademicYear::active();
                if ($subjectModel && $activeYear) {
                    $matchedCs = ClassSubject::firstOrCreate([
                        'class_id' => $classId,
                        'subject_id' => $subjectModel->id,
                        'academic_year_id' => $activeYear->id,
                    ], [
                        'teacher_id' => $sched->teacher_id,
                    ]);
                    $classSubjects->push($matchedCs);
                }
            }

            $sched->class_subject_id = $matchedCs ? $matchedCs->id : null;
        }

        $schedules = $schedulesList->groupBy('day_of_week');

        return view('schedules.index', compact('classes', 'class', 'schedules', 'days', 'classId', 'operationalDays', 'maxDay'));
    }

    public function batchCreate(Request $request)
    {
        $classes = Classes::orderBy('grade')->orderBy('name')->get();
        $classId = $request->get('class_id', $classes->first()?->id);
        $class = Classes::find($classId);

        $teachers = Teacher::active()->orderBy('name')->get();
        $subjects = \App\Models\Subject::orderBy('name')->get();
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
            'items' => 'nullable|array',
        ]);

        $classId = $request->class_id;
        $activeYear = AcademicYear::active();

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $classId, $activeYear) {
            Schedule::where('class_id', $classId)->delete();

            $items = $request->input('items', []);
            foreach ($items as $item) {
                if (empty($item['subject']) || empty($item['teacher_id']) || empty($item['start_time']) || empty($item['end_time'])) {
                    continue;
                }

                Schedule::create([
                    'class_id' => $classId,
                    'teacher_id' => $item['teacher_id'],
                    'subject' => $item['subject'],
                    'day_of_week' => (int) $item['day_of_week'],
                    'start_time' => $item['start_time'],
                    'end_time' => $item['end_time'],
                    'room' => $item['room'] ?? null,
                    'academic_year_id' => $activeYear?->id,
                ]);

                $subjectModel = \App\Models\Subject::where('name', $item['subject'])->first();
                if ($subjectModel && $activeYear) {
                    ClassSubject::updateOrCreate([
                        'class_id' => $classId,
                        'subject_id' => $subjectModel->id,
                        'academic_year_id' => $activeYear->id,
                    ], [
                        'teacher_id' => $item['teacher_id'],
                    ]);

                    $teacher = Teacher::find($item['teacher_id']);
                    if ($teacher) {
                        $teacher->subjects()->syncWithoutDetaching([$subjectModel->id]);
                    }
                }
            }
        });

        $class = Classes::find($classId);
        return redirect()->route('schedules.index', ['class_id' => $classId])
            ->with('success', "Jadwal mingguan untuk Kelas {$class?->name} berhasil disimpan sekaligus!");
    }

    public function copySchedule(Request $request)
    {
        $request->validate([
            'source_class_id' => 'required|exists:classes,id',
            'target_class_id' => 'required|exists:classes,id|different:source_class_id',
        ]);

        $sourceSchedules = Schedule::where('class_id', $request->source_class_id)->get();
        $targetClass = Classes::find($request->target_class_id);
        $activeYear = AcademicYear::active();

        if ($sourceSchedules->isEmpty()) {
            return back()->withErrors(['source_class_id' => 'Kelas asal tidak memiliki jadwal untuk disalin.']);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($sourceSchedules, $request, $activeYear) {
            Schedule::where('class_id', $request->target_class_id)->delete();

            foreach ($sourceSchedules as $sched) {
                Schedule::create([
                    'class_id' => $request->target_class_id,
                    'teacher_id' => $sched->teacher_id,
                    'subject' => $sched->subject,
                    'day_of_week' => $sched->day_of_week,
                    'start_time' => $sched->start_time ? \Carbon\Carbon::parse($sched->start_time)->format('H:i') : '07:15',
                    'end_time' => $sched->end_time ? \Carbon\Carbon::parse($sched->end_time)->format('H:i') : '08:00',
                    'room' => $sched->room,
                    'academic_year_id' => $activeYear?->id,
                ]);

                $subjectModel = \App\Models\Subject::where('name', $sched->subject)->first();
                if ($subjectModel && $activeYear) {
                    ClassSubject::updateOrCreate([
                        'class_id' => $request->target_class_id,
                        'subject_id' => $subjectModel->id,
                        'academic_year_id' => $activeYear->id,
                    ], [
                        'teacher_id' => $sched->teacher_id,
                    ]);
                }
            }
        });

        return redirect()->route('schedules.index', ['class_id' => $request->target_class_id])
            ->with('success', "Jadwal berhasil disalin ke Kelas {$targetClass?->name}!");
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

        // Auto-create/update ClassSubject mapping
        $subjectModel = \App\Models\Subject::where('name', $data['subject'])->first();
        if ($subjectModel && $activeYear) {
            ClassSubject::updateOrCreate([
                'class_id' => $data['class_id'],
                'subject_id' => $subjectModel->id,
                'academic_year_id' => $activeYear->id,
            ], [
                'teacher_id' => $data['teacher_id'],
            ]);

            $teacher = Teacher::find($data['teacher_id']);
            if ($teacher) {
                $teacher->subjects()->syncWithoutDetaching([$subjectModel->id]);
            }
        }

        return redirect()->route('schedules.index', ['class_id' => $data['class_id']])
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Schedule $schedule)
    {
        $classes  = Classes::orderBy('name')->get();
        $teachers = Teacher::active()->with('subjects')->orderBy('name')->get();
        $subjects = \App\Models\Subject::orderBy('name')->get();
        $days     = $this->getOperationalDays();
        $maxDay   = $this->getMaxDayOfWeek();
        
        // Format time to H:i format for easier comparison in select options
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

        // Auto-create/update ClassSubject mapping
        $activeYear = AcademicYear::active();
        $subjectModel = \App\Models\Subject::where('name', $data['subject'])->first();
        if ($subjectModel && $activeYear) {
            ClassSubject::updateOrCreate([
                'class_id' => $data['class_id'],
                'subject_id' => $subjectModel->id,
                'academic_year_id' => $activeYear->id,
            ], [
                'teacher_id' => $data['teacher_id'],
            ]);

            $teacher = Teacher::find($data['teacher_id']);
            if ($teacher) {
                $teacher->subjects()->syncWithoutDetaching([$subjectModel->id]);
            }
        }

        return redirect()->route('schedules.index', ['class_id' => $data['class_id']])
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule)
    {
        $classId = $schedule->class_id;
        $schedule->delete();
        return redirect()->route('schedules.index', ['class_id' => $classId])
            ->with('success', 'Jadwal berhasil dihapus.');
    }
}

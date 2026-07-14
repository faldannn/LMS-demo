<?php

namespace App\Http\Controllers;

use App\Models\{AcademicYear, Classes, ClassSubject, Schedule, Teacher};
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
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
        $days = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

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

        return view('schedules.index', compact('classes', 'class', 'schedules', 'days', 'classId'));
    }

    public function create()
    {
        $classes = Classes::orderBy('name')->get();
        $teachers = Teacher::active()->with('subjects')->orderBy('name')->get();
        $subjects = \App\Models\Subject::orderBy('name')->get();
        $days = ['1' => 'Senin', '2' => 'Selasa', '3' => 'Rabu', '4' => 'Kamis', '5' => 'Jumat', '6' => 'Sabtu'];
        return view('schedules.create', compact('classes', 'teachers', 'subjects', 'days'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'subject' => 'required|string|max:100',
            'day_of_week' => 'required|integer|between:1,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:20',
        ]);
        
        $activeYear = AcademicYear::active();
        $data['academic_year_id'] = $activeYear?->id;
        
        $schedule = Schedule::create($data);

        // Auto-create ClassSubject mapping if not exists
        $subjectModel = \App\Models\Subject::where('name', $data['subject'])->first();
        if ($subjectModel && $activeYear) {
            ClassSubject::firstOrCreate([
                'class_id' => $data['class_id'],
                'subject_id' => $subjectModel->id,
                'academic_year_id' => $activeYear->id,
            ], [
                'teacher_id' => $data['teacher_id'],
            ]);
        }

        return redirect()->route('schedules.index', ['class_id' => $data['class_id']])
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function destroy(Schedule $schedule)
    {
        $classId = $schedule->class_id;
        $schedule->delete();
        return redirect()->route('schedules.index', ['class_id' => $classId])
            ->with('success', 'Jadwal berhasil dihapus.');
    }
}

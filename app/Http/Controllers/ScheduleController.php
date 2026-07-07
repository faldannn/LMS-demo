<?php

namespace App\Http\Controllers;

use App\Models\{AcademicYear, Classes, Schedule, Teacher};
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->isSiswa() && $user->student) {
            $classId = $user->student->class_id;
            $classes = collect([$user->student->class]);
        } else {
            $classId = $request->get('class_id', Classes::first()?->id);
            $classes = Classes::orderBy('grade')->orderBy('name')->get();
        }

        $class = Classes::find($classId);
        $days = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $schedules = Schedule::with('teacher')
            ->where('class_id', $classId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        return view('schedules.index', compact('classes', 'class', 'schedules', 'days', 'classId'));
    }

    public function create()
    {
        $classes = Classes::orderBy('name')->get();
        $teachers = Teacher::active()->orderBy('name')->get();
        $days = ['1' => 'Senin', '2' => 'Selasa', '3' => 'Rabu', '4' => 'Kamis', '5' => 'Jumat', '6' => 'Sabtu'];
        return view('schedules.create', compact('classes', 'teachers', 'days'));
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
        $data['academic_year_id'] = AcademicYear::active()?->id;
        Schedule::create($data);

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

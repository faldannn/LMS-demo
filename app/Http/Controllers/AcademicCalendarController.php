<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAcademicCalendarEventRequest;
use App\Models\AcademicCalendarEvent;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Services\AcademicCalendarService;
use Illuminate\Http\Request;

class AcademicCalendarController extends Controller
{
    public function __construct(private readonly AcademicCalendarService $calendarService)
    {
    }

    public function index(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);
        $events = $this->calendarService->listForUser(auth()->user(), $month, $year);

        return view('academic-calendar.index', compact('events', 'month', 'year'));
    }

    public function create()
    {
        $this->authorize('create', AcademicCalendarEvent::class);
        $academicYears = AcademicYear::orderByDesc('year')->get();
        $classes = Classes::orderBy('name')->get();

        return view('academic-calendar.create', compact('academicYears', 'classes'));
    }

    public function store(StoreAcademicCalendarEventRequest $request)
    {
        $this->authorize('create', AcademicCalendarEvent::class);

        AcademicCalendarEvent::create(array_merge($request->validated(), [
            'created_by' => auth()->id(),
        ]));

        return redirect()->route('academic-calendar.index')->with('success', 'Event kalender berhasil ditambahkan.');
    }

    public function destroy(AcademicCalendarEvent $academicCalendar)
    {
        $this->authorize('delete', $academicCalendar);
        $academicCalendar->delete();

        return redirect()->route('academic-calendar.index')->with('success', 'Event dihapus.');
    }
}

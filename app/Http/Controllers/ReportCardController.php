<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\ReportCard;
use App\Services\ReportCardService;
use Illuminate\Http\Request;

class ReportCardController extends Controller
{
    public function __construct(private readonly ReportCardService $reportCardService) {}

    public function index(Request $request)
    {
        $academicYear = AcademicYear::active();
        $classes = Classes::withCount('students')->get();
        
        $selectedClassId = $request->get('class_id');
        $semester = $request->get('semester', 'ganjil');
        
        $reportCards = collect();
        if ($selectedClassId && $academicYear) {
            $reportCards = ReportCard::with('student')
                ->whereHas('student', function ($query) use ($selectedClassId) {
                    $query->where('class_id', $selectedClassId);
                })
                ->where('academic_year_id', $academicYear->id)
                ->where('semester', $semester)
                ->get();
        }

        return view('report-cards.index', compact('academicYear', 'classes', 'selectedClassId', 'semester', 'reportCards'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'semester' => 'required|in:ganjil,genap'
        ]);
        
        $class = Classes::findOrFail($request->class_id);
        $academicYear = AcademicYear::active();
        
        if (!$academicYear) {
            return redirect()->back()->with('error', 'Tidak ada Tahun Ajaran aktif.');
        }
        
        $result = $this->reportCardService->generateForClass($class, $academicYear, $request->semester);
        
        return redirect()->back()->with('success', "Raport berhasil digenerate untuk {$result['count']} siswa.");
    }
    
    public function show(ReportCard $reportCard)
    {
        $reportCard->load(['student.class', 'academicYear', 'details.subject']);
        return view('report-cards.show', compact('reportCard'));
    }
    
    public function togglePublish(ReportCard $reportCard)
    {
        $reportCard->update([
            'is_published' => !$reportCard->is_published
        ]);
        
        return redirect()->back()->with('success', 'Status publikasi raport berhasil diubah.');
    }
    
    public function print(ReportCard $reportCard)
    {
        $reportCard->load(['student.class', 'academicYear', 'details.subject']);
        return view('report-cards.print', compact('reportCard'));
    }
}

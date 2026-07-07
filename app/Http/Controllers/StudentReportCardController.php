<?php

namespace App\Http\Controllers;

use App\Models\ReportCard;
use Illuminate\Http\Request;

class StudentReportCardController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        $reportCards = ReportCard::with('academicYear')
            ->where('student_id', $student->id)
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('student-report-cards.index', compact('reportCards'));
    }

    public function show(ReportCard $reportCard)
    {
        $student = auth()->user()->student;
        
        if ($reportCard->student_id !== $student->id || !$reportCard->is_published) {
            abort(404);
        }
        
        $reportCard->load(['academicYear', 'details.subject']);
        return view('report-cards.print', compact('reportCard'));
    }
}

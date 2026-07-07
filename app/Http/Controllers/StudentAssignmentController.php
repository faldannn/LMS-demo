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

        $classSubjects = ClassSubject::with(['subject', 'teacher', 'assignments' => function ($query) {
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
}

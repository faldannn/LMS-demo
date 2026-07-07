<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradeAssignmentRequest;
use App\Http\Requests\StoreAssignmentRequest;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassSubject;
use App\Services\AssignmentService;

class AssignmentController extends Controller
{
    public function __construct(private readonly AssignmentService $assignmentService) {}

    public function index(ClassSubject $classSubject)
    {
        $this->authorize('view', $classSubject);

        $assignments = $this->assignmentService->getByClassSubject($classSubject->id);

        return view('assignments.index', compact('classSubject', 'assignments'));
    }

    public function create(ClassSubject $classSubject)
    {
        $this->authorize('manage', $classSubject);

        return view('assignments.create', compact('classSubject'));
    }

    public function store(StoreAssignmentRequest $request, ClassSubject $classSubject)
    {
        $this->authorize('manage', $classSubject);

        $data = $request->validated();
        $data['class_subject_id'] = $classSubject->id;
        $data['published_at'] = !empty($data['is_published']) ? now() : null;

        if ($request->hasFile('file')) {
            $data = array_merge($data, $this->assignmentService->uploadFile($request->file('file')));
        }

        Assignment::create($data);

        return redirect()->route('assignments.index', $classSubject)
            ->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function show(Assignment $assignment)
    {
        $this->authorize('view', $assignment);

        $assignment->load(['classSubject.subject', 'classSubject.class', 'submissions.student', 'submissions.grade']);

        return view('assignments.show', compact('assignment'));
    }

    public function download(Assignment $assignment)
    {
        $this->authorize('view', $assignment);
        abort_unless($assignment->file_path, 404);

        return $this->assignmentService->downloadSubmission($assignment->file_path, $assignment->file_name);
    }

    public function edit(Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $classSubject = $assignment->classSubject;

        return view('assignments.edit', compact('assignment', 'classSubject'));
    }

    public function update(StoreAssignmentRequest $request, Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $data = $request->validated();

        if (!empty($data['is_published']) && !$assignment->is_published) {
            $data['published_at'] = now();
        } elseif (empty($data['is_published'])) {
            $data['published_at'] = null;
        }

        if ($request->hasFile('file')) {
            $this->assignmentService->deleteFile($assignment->file_path);
            $data = array_merge($data, $this->assignmentService->uploadFile($request->file('file')));
        }

        $assignment->update($data);

        return redirect()->route('assignments.index', $assignment->class_subject_id)
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Assignment $assignment)
    {
        $this->authorize('delete', $assignment);

        $classSubjectId = $assignment->class_subject_id;
        $this->assignmentService->deleteFile($assignment->file_path);

        foreach ($assignment->submissions as $submission) {
            $this->assignmentService->deleteFile($submission->file_path);
        }

        $assignment->delete();

        return redirect()->route('assignments.index', $classSubjectId)
            ->with('success', 'Tugas berhasil dihapus.');
    }

    public function togglePublish(Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $isPublished = !$assignment->is_published;
        $assignment->update([
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Status publikasi tugas berhasil diubah.');
    }

    public function gradeSubmissions(Assignment $assignment)
    {
        $this->authorize('grade', $assignment);

        $assignment->load(['submissions.student', 'submissions.grade', 'classSubject.class.students']);

        $students = $assignment->classSubject->class->students()->where('status', true)->get();
        $submissions = $assignment->submissions->keyBy('student_id');

        return view('assignments.grade', compact('assignment', 'students', 'submissions'));
    }

    public function storeGrade(GradeAssignmentRequest $request, AssignmentSubmission $submission)
    {
        $this->authorize('grade', $submission->assignment);

        $data = $request->validated();
        $data['teacher_id'] = auth()->user()->teacher?->id ?? auth()->id();

        if ($submission->grade) {
            $submission->grade->update($data);
        } else {
            $submission->grade()->create($data);
        }

        return redirect()->back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function downloadSubmission(AssignmentSubmission $submission)
    {
        $this->authorize('grade', $submission->assignment);
        abort_unless($submission->file_path, 404);

        return $this->assignmentService->downloadSubmission($submission->file_path, $submission->file_name);
    }
}

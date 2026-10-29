<?php

namespace App\Http\Controllers;

use App\Exports\MasterData\ClassesExport;
use App\Http\Controllers\Concerns\HandlesMasterDataActions;
use App\Http\Requests\StoreClassRequest;
use App\Http\Requests\UpdateClassRequest;
use App\Imports\ClassesImport;
use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\Classes;
use App\Models\Major;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use App\Services\MasterData\ClassService;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    use HandlesMasterDataActions;

    public function __construct(private readonly ClassService $service)
    {
    }

    protected function masterDataService(): ClassService
    {
        return $this->service;
    }

    protected function routePrefix(): string
    {
        return 'classes';
    }

    protected function masterResourceVariable(): string
    {
        return 'classes';
    }

    protected function exportExcelClass(): string
    {
        return ClassesExport::class;
    }

    protected function exportPdfFilename(): string
    {
        return 'Data_Kelas';
    }

    protected function importClass(): ?string
    {
        return ClassesImport::class;
    }

    protected function pdfColumns(): array
    {
        return ['#', 'Kelas', 'Tingkat', 'Jurusan', 'Wali', 'Tahun Ajaran', 'Siswa'];
    }

    protected function pdfRows($items): array
    {
        return $items->values()->map(fn ($c, $i) => [
            $i + 1, $c->name, $c->grade_label ?? $c->grade, $c->major?->name,
            $c->homeroomTeacher?->name, $c->academicYear?->year, $c->students_count ?? 0,
        ])->all();
    }

    public function index(Request $request)
    {
        $classes = $this->service->paginate($request->all());
        $academicYears = AcademicYear::orderByDesc('year')->get();
        $majors = Major::active()->orderBy('name')->get();
        $activeYear = AcademicYear::active();

        return view('classes.index', compact('classes', 'academicYears', 'majors', 'activeYear'));
    }

    public function create()
    {
        $teachers = Teacher::active()->orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('is_active')->orderByDesc('year')->get();
        $majors = Major::active()->orderBy('name')->get();

        return view('classes.create', compact('teachers', 'academicYears', 'majors'));
    }

    public function store(StoreClassRequest $request)
    {
        $data = $request->validated();
        $this->service->create($data);

        return redirect()->route('classes.index')->with('success', "Kelas {$data['name']} berhasil ditambahkan.");
    }

    public function show(Classes $class)
    {
        $class->load([
            'homeroomTeacher', 'major', 'academicYear',
            'students' => fn ($q) => $q->active()->orderBy('name'),
            'schedules.teacher',
            'classSubjects.subject',
            'classSubjects.teacher',
        ]);

        $subjects = Subject::orderBy('name')->get();
        $teachers = Teacher::active()->with('subjects')->orderBy('name')->get();

        return view('classes.show', compact('class', 'subjects', 'teachers'));
    }

    public function storeSubject(Request $request, Classes $class)
    {
        $data = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        $teacher = Teacher::with('subjects')->findOrFail($data['teacher_id']);
        if (!$teacher->subjects->contains($data['subject_id'])) {
            $teacher->subjects()->syncWithoutDetaching([$data['subject_id']]);
        }

        $activeYear = AcademicYear::active();
        if (!$activeYear) {
            return back()->withErrors(['error' => 'Tidak ada tahun ajaran aktif.']);
        }

        ClassSubject::updateOrCreate([
            'class_id' => $class->id,
            'subject_id' => $data['subject_id'],
            'academic_year_id' => $activeYear->id,
        ], ['teacher_id' => $data['teacher_id']]);

        // Synchronize matching schedules to the new teacher
        $subject = Subject::find($data['subject_id']);
        if ($subject) {
            Schedule::where('class_id', $class->id)
                ->where(function ($q) use ($subject) {
                    $q->where('subject', $subject->name)
                      ->orWhere('subject', 'like', "%{$subject->name}%");
                })
                ->update(['teacher_id' => $data['teacher_id']]);
        }

        return back()->with('success', 'Mata pelajaran berhasil ditambahkan ke kelas ini.');
    }

    public function destroySubject(Classes $class, ClassSubject $classSubject)
    {
        $classSubject->delete();

        return back()->with('success', 'Mata pelajaran berhasil dihapus dari kelas ini.');
    }

    public function edit(Classes $class)
    {
        $teachers = Teacher::active()->orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('year')->get();
        $majors = Major::active()->orderBy('name')->get();

        return view('classes.edit', compact('class', 'teachers', 'academicYears', 'majors'));
    }

    public function update(UpdateClassRequest $request, Classes $class)
    {
        $this->service->update($class, $request->validated());

        return redirect()->route('classes.index')->with('success', "Kelas {$class->name} berhasil diperbarui.");
    }

    public function destroy(Classes $class)
    {
        try {
            $name = $class->name;
            $this->service->delete($class);

            return redirect()->route('classes.index')->with('success', "Kelas {$name} berhasil dihapus.");
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function promote(Request $request)
    {
        $data = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'action_type' => 'nullable|in:naik,tinggal,lulus',
            'target_class_id' => 'required|string',
            'notes' => 'nullable|string|max:255',
        ]);

        $actionType = $data['action_type'] ?? ($data['target_class_id'] === 'lulus' ? 'lulus' : 'naik');

        return \Illuminate\Support\Facades\DB::transaction(function () use ($data, $actionType) {
            $students = \App\Models\Student::with(['class', 'user'])->whereIn('id', $data['student_ids'])->get();

            if ($actionType === 'lulus' || $data['target_class_id'] === 'lulus') {
                foreach ($students as $student) {
                    $currentAyId = $student->class?->academic_year_id ?? \App\Models\AcademicYear::active()?->id;

                    if ($currentAyId) {
                        \App\Models\StudentAcademicHistory::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'academic_year_id' => $currentAyId,
                            ],
                            [
                                'class_id' => $student->class_id,
                                'status' => 'lulus',
                                'notes' => $data['notes'] ?: 'Dinyatakan Lulus / Alumni',
                            ]
                        );
                    }

                    $student->update([
                        'status' => false,
                        'student_status' => 'lulus',
                        'exit_date' => now()->toDateString(),
                        'exit_reason' => 'Lulus Pendidikan / Alumni',
                    ]);

                    if ($student->user) {
                        $student->user->update(['status' => false]);
                    }
                }

                return back()->with('success', count($students) . ' siswa berhasil diluluskan (status alumni tercatat di riwayat akademik).');
            }

            // Target class validation
            $targetClass = \App\Models\Classes::with('academicYear')->findOrFail((int) $data['target_class_id']);

            foreach ($students as $student) {
                $oldClass = $student->class;
                $sourceAyId = $oldClass?->academic_year_id ?? \App\Models\AcademicYear::active()?->id;
                $targetAyId = $targetClass->academic_year_id;

                // 1. Record historical completion on source academic year if source and target differ
                if ($sourceAyId && $sourceAyId != $targetAyId) {
                    $histStatus = ($actionType === 'tinggal') ? 'tinggal_kelas' : 'naik_kelas';
                    $histNote = ($actionType === 'tinggal')
                        ? ('Tinggal Kelas (Mengulang). ' . ($data['notes'] ?? ''))
                        : ('Naik ke kelas ' . $targetClass->name . '. ' . ($data['notes'] ?? ''));

                    \App\Models\StudentAcademicHistory::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'academic_year_id' => $sourceAyId,
                        ],
                        [
                            'class_id' => $student->class_id,
                            'status' => $histStatus,
                            'notes' => trim($histNote),
                        ]
                    );
                }

                // 2. Update current student placement
                $student->update([
                    'class_id' => $targetClass->id,
                    'status' => true,
                    'student_status' => 'aktif',
                ]);

                // 3. Record new academic year placement as active
                if ($targetAyId) {
                    $newPlacementNote = ($actionType === 'tinggal')
                        ? ('Tinggal Kelas di ' . $targetClass->name)
                        : ('Ditempatkan di ' . $targetClass->name . ($oldClass ? ' dari ' . $oldClass->name : ''));

                    \App\Models\StudentAcademicHistory::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'academic_year_id' => $targetAyId,
                        ],
                        [
                            'class_id' => $targetClass->id,
                            'status' => 'aktif',
                            'notes' => trim($newPlacementNote . ($data['notes'] ? ' - ' . $data['notes'] : '')),
                        ]
                    );
                }
            }

            $actionLabel = match ($actionType) {
                'tinggal' => 'Tinggal kelas (mengulang)',
                default => 'Naik/pindah kelas',
            };

            return back()->with('success', count($students) . " siswa berhasil diproses {$actionLabel} ke {$targetClass->name} beserta pembaruan riwayat akademik.");
        });
    }
}

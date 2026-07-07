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
        $teachers = Teacher::active()->orderBy('name')->get();

        return view('classes.show', compact('class', 'subjects', 'teachers'));
    }

    public function storeSubject(Request $request, Classes $class)
    {
        $data = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        $activeYear = AcademicYear::active();
        if (!$activeYear) {
            return back()->withErrors(['error' => 'Tidak ada tahun ajaran aktif.']);
        }

        ClassSubject::firstOrCreate([
            'class_id' => $class->id,
            'subject_id' => $data['subject_id'],
            'academic_year_id' => $activeYear->id,
        ], ['teacher_id' => $data['teacher_id']]);

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
}

<?php

namespace App\Http\Controllers;

use App\Exports\MasterData\TeachersExport;
use App\Http\Controllers\Concerns\HandlesMasterDataActions;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Imports\TeachersImport;
use App\Models\Teacher;
use App\Services\MasterData\TeacherService;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    use HandlesMasterDataActions;

    public function __construct(private readonly TeacherService $service)
    {
    }

    protected function masterDataService(): TeacherService
    {
        return $this->service;
    }

    protected function routePrefix(): string
    {
        return 'teachers';
    }

    protected function masterResourceVariable(): string
    {
        return 'teachers';
    }

    protected function exportExcelClass(): string
    {
        return TeachersExport::class;
    }

    protected function exportPdfFilename(): string
    {
        return 'Data_Guru';
    }

    protected function importClass(): ?string
    {
        return TeachersImport::class;
    }

    protected function pdfColumns(): array
    {
        return ['#', 'NIP', 'Nama', 'Mapel', 'Telepon', 'Email', 'Status'];
    }

    protected function pdfRows($items): array
    {
        return $items->values()->map(fn ($t, $i) => [
            $i + 1, $t->nip, $t->name, $t->subject, $t->phone, $t->email, $t->status ? 'Aktif' : 'Non-Aktif',
        ])->all();
    }

    public function index(Request $request)
    {
        $teachers = $this->service->paginate($request->all());

        return view('teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('teachers.create');
    }

    public function store(StoreTeacherRequest $request)
    {
        $teacher = $this->service->create($request->validated());
        $msg = "Guru {$teacher->name} berhasil ditambahkan.";
        if ($teacher->generated_username ?? null) {
            $msg .= " Username: {$teacher->generated_username}, Password: {$teacher->generated_password}";
        }

        return redirect()->route('teachers.index')->with('success', $msg);
    }

    public function show(Teacher $teacher)
    {
        $teacher->load(['homeroomClasses', 'schedules.class', 'attendances' => fn ($q) => $q->latest()->limit(10)]);

        return view('teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        return view('teachers.edit', compact('teacher'));
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $this->service->update($teacher, $request->validated());

        return redirect()->route('teachers.index')->with('success', "Data guru {$teacher->name} berhasil diperbarui.");
    }

    public function destroy(Teacher $teacher)
    {
        $name = $teacher->name;
        $this->service->delete($teacher);

        return redirect()->route('teachers.index')->with('success', "Guru {$name} berhasil dihapus.");
    }
}

<?php

namespace App\Http\Controllers;

use App\Exports\MasterData\SubjectsExport;
use App\Http\Controllers\Concerns\HandlesMasterDataActions;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Imports\SubjectsImport;
use App\Models\Subject;
use App\Services\MasterData\SubjectService;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    use HandlesMasterDataActions;

    public function __construct(private readonly SubjectService $service)
    {
    }

    protected function masterDataService(): SubjectService
    {
        return $this->service;
    }

    protected function routePrefix(): string
    {
        return 'subjects';
    }

    protected function masterResourceVariable(): string
    {
        return 'subjects';
    }

    protected function exportExcelClass(): string
    {
        return SubjectsExport::class;
    }

    protected function exportPdfFilename(): string
    {
        return 'Data_Mata_Pelajaran';
    }

    protected function importClass(): ?string
    {
        return SubjectsImport::class;
    }

    protected function pdfColumns(): array
    {
        return ['#', 'Kode', 'Nama', 'Kelompok', 'Tingkat', 'Status'];
    }

    protected function pdfRows($items): array
    {
        return $items->values()->map(fn ($s, $i) => [
            $i + 1, $s->code, $s->name, $s->group, $s->grade, $s->status ? 'Aktif' : 'Non-Aktif',
        ])->all();
    }

    public function index(Request $request)
    {
        $subjects = $this->service->paginate($request->all());

        return view('subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('subjects.create');
    }

    public function store(StoreSubjectRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function show(Subject $subject)
    {
        $subject->loadCount('classSubjects');

        return view('subjects.show', compact('subject'));
    }

    public function edit(Subject $subject)
    {
        return view('subjects.edit', compact('subject'));
    }

    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        $this->service->update($subject, $request->validated());

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject)
    {
        $this->service->delete($subject);

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Exports\MasterData\SemestersExport;
use App\Http\Controllers\Concerns\HandlesMasterDataActions;
use App\Http\Requests\StoreSemesterRequest;
use App\Http\Requests\UpdateSemesterRequest;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Services\MasterData\SemesterService;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    use HandlesMasterDataActions;

    public function __construct(private readonly SemesterService $service)
    {
    }

    protected function masterDataService(): SemesterService
    {
        return $this->service;
    }

    protected function routePrefix(): string
    {
        return 'semesters';
    }

    protected function masterResourceVariable(): string
    {
        return 'semesters';
    }

    protected function exportExcelClass(): string
    {
        return SemestersExport::class;
    }

    protected function exportPdfFilename(): string
    {
        return 'Data_Semester';
    }

    protected function pdfColumns(): array
    {
        return ['#', 'Tahun Ajaran', 'Semester', 'Mulai', 'Selesai', 'Status'];
    }

    protected function pdfRows($items): array
    {
        return $items->values()->map(fn ($s, $i) => [
            $i + 1, $s->academicYear?->year, $s->label,
            $s->start_date?->format('d/m/Y'), $s->end_date?->format('d/m/Y'),
            $s->is_active ? 'Aktif' : 'Non-Aktif',
        ])->all();
    }

    public function index(Request $request)
    {
        $semesters = $this->service->paginate($request->all());
        $academicYears = AcademicYear::orderByDesc('year')->get();

        return view('semesters.index', compact('semesters', 'academicYears'));
    }

    public function create()
    {
        $academicYears = AcademicYear::orderByDesc('year')->get();

        return view('semesters.create', compact('academicYears'));
    }

    public function store(StoreSemesterRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('semesters.index')->with('success', 'Semester berhasil ditambahkan.');
    }

    public function show(Semester $semester)
    {
        $semester->load('academicYear');

        return view('semesters.show', compact('semester'));
    }

    public function edit(Semester $semester)
    {
        $academicYears = AcademicYear::orderByDesc('year')->get();

        return view('semesters.edit', compact('semester', 'academicYears'));
    }

    public function update(UpdateSemesterRequest $request, Semester $semester)
    {
        $this->service->update($semester, $request->validated());

        return redirect()->route('semesters.index')->with('success', 'Semester berhasil diperbarui.');
    }

    public function destroy(Semester $semester)
    {
        $this->service->delete($semester);

        return redirect()->route('semesters.index')->with('success', 'Semester berhasil dihapus.');
    }
}

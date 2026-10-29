<?php

namespace App\Http\Controllers;

use App\Exports\MasterData\AcademicYearsExport;
use App\Http\Controllers\Concerns\HandlesMasterDataActions;
use App\Http\Requests\StoreAcademicYearRequest;
use App\Http\Requests\UpdateAcademicYearRequest;
use App\Models\AcademicYear;
use App\Services\MasterData\AcademicYearService;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    use HandlesMasterDataActions;

    public function __construct(private readonly AcademicYearService $service)
    {
    }

    protected function masterDataService(): AcademicYearService
    {
        return $this->service;
    }

    protected function routePrefix(): string
    {
        return 'academic-years';
    }

    protected function masterResourceVariable(): string
    {
        return 'academicYears';
    }

    protected function exportExcelClass(): string
    {
        return AcademicYearsExport::class;
    }

    protected function exportPdfFilename(): string
    {
        return 'Data_Tahun_Ajaran';
    }

    protected function pdfColumns(): array
    {
        return ['#', 'Tahun', 'Deskripsi', 'Semester', 'Kelas', 'Status'];
    }

    protected function pdfRows($items): array
    {
        return $items->values()->map(fn ($y, $i) => [
            $i + 1, $y->year, $y->description, $y->semesters_count ?? 0, $y->classes_count ?? 0, $y->is_active ? 'Aktif' : 'Non-Aktif',
        ])->all();
    }

    public function index(Request $request)
    {
        $academicYears = $this->service->paginate($request->all());
        $allYears = AcademicYear::orderByDesc('year')->get();

        return view('academic-years.index', compact('academicYears', 'allYears'));
    }

    public function create()
    {
        return view('academic-years.create');
    }

    public function store(StoreAcademicYearRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('academic-years.index')->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function show(AcademicYear $academicYear)
    {
        $academicYear->load([
            'semesters',
            'classes' => fn ($q) => $q->with(['homeroomTeacher', 'major'])->withCount('students')->orderBy('grade')->orderBy('name'),
        ]);

        $allYears = AcademicYear::where('id', '!=', $academicYear->id)->orderByDesc('year')->get();

        return view('academic-years.show', compact('academicYear', 'allYears'));
    }

    public function duplicateClasses(Request $request, AcademicYear $academicYear)
    {
        $data = $request->validate([
            'source_academic_year_id' => 'required|exists:academic_years,id|different:academic_year_id',
            'copy_homeroom' => 'nullable|boolean',
        ], [
            'source_academic_year_id.different' => 'Tahun ajaran sumber tidak boleh sama dengan tahun ajaran tujuan.',
        ]);

        try {
            $res = $this->service->duplicateClasses(
                (int) $data['source_academic_year_id'],
                $academicYear->id,
                $request->boolean('copy_homeroom', true)
            );

            return back()->with('success', "Struktur kelas untuk {$academicYear->year} berhasil disiapkan: {$res['created']} kelas disalin ({$res['skipped']} kelas sudah ada).");
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('academic-years.edit', compact('academicYear'));
    }

    public function update(UpdateAcademicYearRequest $request, AcademicYear $academicYear)
    {
        $this->service->update($academicYear, $request->validated());

        return redirect()->route('academic-years.index')->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        try {
            $this->service->delete($academicYear);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('academic-years.index')->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}

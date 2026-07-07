<?php

namespace App\Http\Controllers;

use App\Exports\MasterData\MajorsExport;
use App\Http\Controllers\Concerns\HandlesMasterDataActions;
use App\Http\Requests\StoreMajorRequest;
use App\Http\Requests\UpdateMajorRequest;
use App\Imports\MajorsImport;
use App\Models\Major;
use App\Services\MasterData\MajorService;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    use HandlesMasterDataActions;

    public function __construct(private readonly MajorService $service)
    {
    }

    protected function masterDataService(): MajorService
    {
        return $this->service;
    }

    protected function routePrefix(): string
    {
        return 'majors';
    }

    protected function masterResourceVariable(): string
    {
        return 'majors';
    }

    protected function exportExcelClass(): string
    {
        return MajorsExport::class;
    }

    protected function exportPdfFilename(): string
    {
        return 'Data_Jurusan';
    }

    protected function importClass(): ?string
    {
        return MajorsImport::class;
    }

    protected function pdfColumns(): array
    {
        return ['#', 'Kode', 'Nama', 'Deskripsi', 'Kelas', 'Status'];
    }

    protected function pdfRows($items): array
    {
        return $items->values()->map(fn ($m, $i) => [
            $i + 1, $m->code, $m->name, $m->description, $m->classes_count ?? 0, $m->status ? 'Aktif' : 'Non-Aktif',
        ])->all();
    }

    public function index(Request $request)
    {
        $majors = $this->service->paginate($request->all());

        return view('majors.index', compact('majors'));
    }

    public function create()
    {
        return view('majors.create');
    }

    public function store(StoreMajorRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('majors.index')->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function show(Major $major)
    {
        $major->loadCount('classes');

        return view('majors.show', compact('major'));
    }

    public function edit(Major $major)
    {
        return view('majors.edit', compact('major'));
    }

    public function update(UpdateMajorRequest $request, Major $major)
    {
        $this->service->update($major, $request->validated());

        return redirect()->route('majors.index')->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Major $major)
    {
        try {
            $this->service->delete($major);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('majors.index')->with('success', 'Jurusan berhasil dihapus.');
    }
}

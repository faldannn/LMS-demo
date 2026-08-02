<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait HandlesMasterDataActions
{
    abstract protected function exportExcelClass(): string;

    abstract protected function exportPdfFilename(): string;

    abstract protected function masterDataService();

    abstract protected function routePrefix(): string;

    abstract protected function pdfColumns(): array;

    abstract protected function pdfRows($items): array;

    protected function importClass(): ?string
    {
        return null;
    }

    protected function masterResourceVariable(): string
    {
        return 'items';
    }

    public function trashed(Request $request)
    {
        $filters = array_merge($request->all(), ['trashed' => true]);
        $items = $this->masterDataService()->paginate($filters);

        return view($this->routePrefix() . '.trashed', [
            $this->masterResourceVariable() => $items,
            'filters' => $filters,
        ]);
    }

    public function restore(int $id)
    {
        $this->masterDataService()->restore($id);

        return redirect()->route($this->routePrefix() . '.index')
            ->with('success', 'Data berhasil dipulihkan.');
    }

    public function forceDestroy(int $id)
    {
        $this->masterDataService()->forceDelete($id);

        return redirect()->route($this->routePrefix() . '.trashed')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        return Excel::download(
            new ($this->exportExcelClass())($request->all()),
            $this->exportPdfFilename() . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $items = $this->masterDataService()->exportCollection($request->all());

        $pdf = Pdf::loadView('exports.master-data-pdf', [
            'title' => $this->exportPdfFilename(),
            'columns' => $this->pdfColumns(),
            'items' => collect($this->pdfRows($items)),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($this->exportPdfFilename() . '.pdf');
    }

    public function import(Request $request)
    {
        $importClass = $this->importClass();
        if (!$importClass) {
            abort(404);
        }

        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:4096']);

        try {
            $importInstance = new $importClass();
            Excel::import($importInstance, $request->file('file'));

            if (!empty($importInstance->importedIds)) {
                $type = $this->routePrefix() === 'teachers' ? 'teacher' : 'student';
                \App\Jobs\SyncCbtAfterImport::dispatch($type, $importInstance->importedIds);

                if ($type === 'student') {
                    try {
                        app(\App\Services\RiansaKartuService::class)->syncMembers();
                    } catch (\Throwable $ex) {
                        \Illuminate\Support\Facades\Log::warning('Auto sync Riansa Kartu after import failed: ' . $ex->getMessage());
                    }
                }
            }

            return redirect()->route($this->routePrefix() . '.index')
                ->with('success', 'Import data berhasil diselesaikan & disinkronkan ke Riansa Kartu.');

        } catch (\Throwable $e) {
            return back()->withErrors(['file' => 'Import gagal: ' . $e->getMessage()]);
        }
    }
}

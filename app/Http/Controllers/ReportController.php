<?php

namespace App\Http\Controllers;

use App\Services\Reports\ComprehensiveReportService;
use App\Support\ReportFilter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function __construct(
        private ComprehensiveReportService $reportService
    ) {}

    /**
     * Hub laporan analitik (index).
     */
    public function index()
    {
        $types = config('reports.types', []);

        return view('reports.index', compact('types'));
    }

    /**
     * Tampilkan laporan berdasarkan tipe.
     */
    public function show(Request $request, string $type)
    {
        abort_unless(array_key_exists($type, config('reports.types', [])), 404);

        $filter = ReportFilter::fromRequest($request);
        $data   = $this->reportService->forType($type, $filter);

        $typeConfig = config("reports.types.{$type}");

        return view('reports.show', [
            'type'       => $type,
            'typeConfig' => $typeConfig,
            'filter'     => $filter,
            'data'       => $data,
            'allTypes'   => config('reports.types', []),
        ]);
    }

    /**
     * Export PDF.
     */
    public function pdf(Request $request, string $type)
    {
        abort_unless(array_key_exists($type, config('reports.types', [])), 404);

        $filter = ReportFilter::fromRequest($request);
        $data   = $this->reportService->forType($type, $filter);

        $typeConfig = config("reports.types.{$type}");

        return response()->view('reports.pdf', [
            'type'       => $type,
            'typeConfig' => $typeConfig,
            'filter'     => $filter,
            'data'       => $data,
        ])->header('Content-Type', 'text/html');
    }

    /**
     * Export Excel (CSV sederhana).
     */
    public function excel(Request $request, string $type)
    {
        abort_unless(array_key_exists($type, config('reports.types', [])), 404);

        $filter = ReportFilter::fromRequest($request);
        $data   = $this->reportService->forType($type, $filter);

        $typeConfig = config("reports.types.{$type}");
        $rows       = $data['rows'] ?? collect();
        $filename   = 'laporan-' . $type . '-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rows, $typeConfig) {
            $handle = fopen('php://output', 'w');

            // BOM untuk Excel UTF-8
            fputs($handle, "\xEF\xBB\xBF");

            if ($rows->isNotEmpty()) {
                fputcsv($handle, array_keys($rows->first()));
                foreach ($rows as $row) {
                    fputcsv($handle, array_values($row));
                }
            } else {
                fputcsv($handle, ['Tidak ada data']);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}

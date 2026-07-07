<?php

namespace App\Exports\MasterData;

use App\Services\MasterData\SemesterService;
use Illuminate\Support\Collection;

class SemestersExport extends BaseMasterDataExport
{
    protected function fetchCollection(): Collection
    {
        return app(SemesterService::class)->exportCollection($this->filters);
    }

    public function headings(): array
    {
        return ['#', 'Tahun Ajaran', 'Semester', 'Mulai', 'Selesai', 'Status'];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [
            $i, $row->academicYear?->year, $row->label,
            $row->start_date?->format('d/m/Y'), $row->end_date?->format('d/m/Y'),
            $row->is_active ? 'Aktif' : 'Non-Aktif',
        ];
    }
}

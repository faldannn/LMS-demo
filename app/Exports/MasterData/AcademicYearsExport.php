<?php

namespace App\Exports\MasterData;

use App\Services\MasterData\AcademicYearService;
use Illuminate\Support\Collection;

class AcademicYearsExport extends BaseMasterDataExport
{
    protected function fetchCollection(): Collection
    {
        return app(AcademicYearService::class)->exportCollection($this->filters);
    }

    public function headings(): array
    {
        return ['#', 'Tahun Ajaran', 'Deskripsi', 'Jumlah Semester', 'Jumlah Kelas', 'Status'];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [
            $i, $row->year, $row->description,
            $row->semesters_count ?? 0, $row->classes_count ?? 0,
            $row->is_active ? 'Aktif' : 'Non-Aktif',
        ];
    }
}

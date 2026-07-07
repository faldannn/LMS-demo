<?php

namespace App\Exports\MasterData;

use App\Services\MasterData\SubjectService;
use Illuminate\Support\Collection;

class SubjectsExport extends BaseMasterDataExport
{
    protected function fetchCollection(): Collection
    {
        return app(SubjectService::class)->exportCollection($this->filters);
    }

    public function headings(): array
    {
        return ['#', 'Kode', 'Nama', 'Kelompok', 'Tingkat', 'Status'];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [$i, $row->code, $row->name, $row->group, $row->grade, $row->status ? 'Aktif' : 'Non-Aktif'];
    }
}

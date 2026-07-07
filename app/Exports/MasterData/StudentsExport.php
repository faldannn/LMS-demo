<?php

namespace App\Exports\MasterData;

use App\Services\MasterData\StudentService;
use Illuminate\Support\Collection;

class StudentsExport extends BaseMasterDataExport
{
    protected function fetchCollection(): Collection
    {
        return app(StudentService::class)->exportCollection($this->filters);
    }

    public function headings(): array
    {
        return ['#', 'NIS', 'NISN', 'Nama', 'Kelas', 'JK', 'Ortu', 'HP Ortu', 'Status'];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [
            $i, $row->nis, $row->nisn, $row->name,
            $row->class?->name, $row->gender === 'L' ? 'L' : 'P',
            $row->parent_name, $row->parent_phone, $row->status ? 'Aktif' : 'Non-Aktif',
        ];
    }
}

<?php

namespace App\Exports\MasterData;

use App\Services\MasterData\TeacherService;
use Illuminate\Support\Collection;

class TeachersExport extends BaseMasterDataExport
{
    protected function fetchCollection(): Collection
    {
        return app(TeacherService::class)->exportCollection($this->filters);
    }

    public function headings(): array
    {
        return ['#', 'NIP', 'Nama', 'Mapel', 'Telepon', 'Email', 'Status'];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [$i, $row->nip, $row->name, $row->subject, $row->phone, $row->email, $row->status ? 'Aktif' : 'Non-Aktif'];
    }
}

<?php

namespace App\Exports\MasterData;

use App\Services\MasterData\MajorService;
use Illuminate\Support\Collection;

class MajorsExport extends BaseMasterDataExport
{
    protected function fetchCollection(): Collection
    {
        return app(MajorService::class)->exportCollection($this->filters);
    }

    public function headings(): array
    {
        return ['#', 'Kode', 'Nama Jurusan', 'Deskripsi', 'Jumlah Kelas', 'Status'];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [$i, $row->code, $row->name, $row->description, $row->classes_count ?? 0, $row->status ? 'Aktif' : 'Non-Aktif'];
    }
}

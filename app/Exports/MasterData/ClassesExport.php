<?php

namespace App\Exports\MasterData;

use App\Services\MasterData\ClassService;
use Illuminate\Support\Collection;

class ClassesExport extends BaseMasterDataExport
{
    protected function fetchCollection(): Collection
    {
        return app(ClassService::class)->exportCollection($this->filters);
    }

    public function headings(): array
    {
        return ['#', 'Nama Kelas', 'Tingkat', 'Jurusan', 'Wali Kelas', 'Tahun Ajaran', 'Ruang', 'Jumlah Siswa'];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [
            $i, $row->name, $row->grade_label ?? $row->grade,
            $row->major?->name, $row->homeroomTeacher?->name,
            $row->academicYear?->year, $row->room, $row->students_count ?? 0,
        ];
    }
}

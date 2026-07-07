<?php

namespace App\Imports;

use App\Models\Classes;
use App\Models\Major;
use App\Models\Teacher;
use App\Models\AcademicYear;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ClassesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use Importable;

    public function model(array $row): ?Classes
    {
        $year = AcademicYear::where('year', $row['tahun_ajaran'] ?? '')->first();
        $teacher = Teacher::where('nip', $row['nip_wali'] ?? '')->orWhere('name', $row['wali_kelas'] ?? '')->first();
        $major = Major::where('code', $row['kode_jurusan'] ?? '')->orWhere('name', $row['jurusan'] ?? '')->first();

        if (!$year) {
            return null;
        }

        return new Classes([
            'name' => $row['nama_kelas'],
            'grade' => (int) $row['tingkat'],
            'major_id' => $major?->id,
            'homeroom_teacher_id' => $teacher?->id,
            'academic_year_id' => $year->id,
            'room' => $row['ruang'] ?? null,
            'max_students' => (int) ($row['kapasitas'] ?? 36),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_kelas' => 'required|string',
            'tingkat' => 'required|integer|in:10,11,12',
            'tahun_ajaran' => 'required|string',
        ];
    }

    public function onError(\Throwable $e): void
    {
        \Log::warning('Class import error: ' . $e->getMessage());
    }
}

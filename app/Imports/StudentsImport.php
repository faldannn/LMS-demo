<?php

namespace App\Imports;

use App\Models\{Classes, Student};
use Maatwebsite\Excel\Concerns\{ToModel, WithHeadingRow, WithValidation, SkipsOnError};
use Maatwebsite\Excel\Concerns\Importable;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use Importable;

    public function model(array $row): ?Student
    {
        $class = Classes::where('name', $row['kelas'] ?? '')->first();
        if (!$class)
            return null;

        return new Student([
            'nis' => $row['nis'],
            'nisn' => $row['nisn'] ?? null,
            'name' => $row['nama'],
            'gender' => strtoupper($row['jk'] ?? 'L') === 'L' ? 'L' : 'P',
            'dob' => $row['tanggal_lahir'] ?? null,
            'class_id' => $class->id,
            'parent_name' => $row['nama_ortu'] ?? null,
            'parent_phone' => $row['no_hp_ortu'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nis' => 'required|unique:students,nis',
            'nama' => 'required|string',
            'kelas' => 'required|string',
        ];
    }

    public function onError(\Throwable $e): void
    {
        \Log::warning('Import error: ' . $e->getMessage());
    }
}


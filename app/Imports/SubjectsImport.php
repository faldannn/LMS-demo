<?php

namespace App\Imports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SubjectsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use Importable;

    public function model(array $row): Subject
    {
        return new Subject([
            'code' => $row['kode'],
            'name' => $row['nama'],
            'group' => $row['kelompok'] ?? null,
            'grade' => $row['tingkat'] ?? null,
            'description' => $row['deskripsi'] ?? null,
            'status' => true,
        ]);
    }

    public function rules(): array
    {
        return [
            'kode' => 'required|unique:subjects,code',
            'nama' => 'required|string',
        ];
    }

    public function onError(\Throwable $e): void
    {
        \Log::warning('Subject import error: ' . $e->getMessage());
    }
}

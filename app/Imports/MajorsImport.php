<?php

namespace App\Imports;

use App\Models\Major;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MajorsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use Importable;

    public function model(array $row): Major
    {
        return new Major([
            'code' => strtoupper($row['kode']),
            'name' => $row['nama'],
            'description' => $row['deskripsi'] ?? null,
            'status' => true,
        ]);
    }

    public function rules(): array
    {
        return [
            'kode' => 'required|unique:majors,code',
            'nama' => 'required|string',
        ];
    }

    public function onError(\Throwable $e): void
    {
        \Log::warning('Major import error: ' . $e->getMessage());
    }
}

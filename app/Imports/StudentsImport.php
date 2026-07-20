<?php

namespace App\Imports;

use App\Models\{Classes, Student, User};
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\{ToModel, WithHeadingRow, WithValidation, SkipsOnError};
use Maatwebsite\Excel\Concerns\Importable;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use Importable;

    public array $importedIds = [];

    public function model(array $row): ?Student
    {
        $class = Classes::where('name', $row['kelas'] ?? '')->first();
        if (!$class)
            return null;

        $dob = null;
        if (!empty($row['tanggal_lahir'])) {
            if (is_numeric($row['tanggal_lahir'])) {
                $dob = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_lahir'])->format('Y-m-d');
            } else {
                $dob = date('Y-m-d', strtotime($row['tanggal_lahir']));
            }
        }
        $password = $dob ? $dob : (string) $row['nis'];

        $student = User::withoutEvents(function () use ($row, $class, $dob, $password) {
            $user = User::create([
                'username' => $row['nis'],
                'password' => Hash::make($password),
                'name' => $row['nama'],
                'role' => UserRole::Siswa,
                'status' => true,
            ]);

            return Student::withoutEvents(function () use ($row, $class, $dob, $user) {
                return Student::create([
                    'nis' => $row['nis'],
                    'nisn' => $row['nisn'] ?? null,
                    'name' => $row['nama'],
                    'gender' => strtoupper($row['jk'] ?? 'L') === 'L' ? 'L' : 'P',
                    'dob' => $dob,
                    'class_id' => $class->id,
                    'parent_name' => $row['nama_ortu'] ?? null,
                    'parent_phone' => $row['no_hp_ortu'] ?? null,
                    'user_id' => $user->id,
                    'status' => true,
                ]);
            });
        });

        if ($student) {
            $this->importedIds[] = $student->id;
        }

        return null;
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
        Log::warning('Import error: ' . $e->getMessage());
    }
}


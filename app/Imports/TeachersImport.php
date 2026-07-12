<?php

namespace App\Imports;

use App\Enums\UserRole;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TeachersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use Importable;

    private const DEFAULT_PASSWORD = 'guru123';

    public function model(array $row): ?Teacher
    {
        $username = $row['nip'] ?? $row['email'] ?? Str::slug($row['nama'], '.');
        $user = User::create([
            'username' => $username,
            'password' => Hash::make($row['password'] ?? self::DEFAULT_PASSWORD),
            'name' => $row['nama'],
            'email' => $row['email'] ?? null,
            'role' => UserRole::Guru,
            'status' => true,
        ]);

        return new Teacher([
            'nip' => $row['nip'] ?? null,
            'name' => $row['nama'],
            'subject' => $row['mapel'] ?? null,
            'phone' => $row['telepon'] ?? null,
            'email' => $row['email'] ?? null,
            'user_id' => $user->id,
            'status' => true,
        ]);
    }

    public function rules(): array
    {
        return ['nama' => 'required|string'];
    }

    public function onError(\Throwable $e): void
    {
        \Log::warning('Teacher import error: ' . $e->getMessage());
    }
}

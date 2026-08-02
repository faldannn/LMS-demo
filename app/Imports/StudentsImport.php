<?php

namespace App\Imports;

use App\Models\{AcademicYear, Classes, Student, User};
use App\Enums\UserRole;
use App\Services\RiansaKartuService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\{ToModel, WithHeadingRow, WithValidation, SkipsOnError};
use Maatwebsite\Excel\Concerns\Importable;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use Importable;

    public array $importedIds = [];

    private function resolveGrade(string $className): int
    {
        $className = strtoupper(trim($className));
        if (str_starts_with($className, 'XII')) return 12;
        if (str_starts_with($className, 'XI')) return 11;
        if (str_starts_with($className, 'X')) return 10;
        return 10;
    }

    public function model(array $row): ?Student
    {
        $rawNisn = trim((string)($row['nisn'] ?? $row['nis'] ?? ''));
        $nama = trim((string)($row['nama_lengkap'] ?? $row['nama'] ?? ''));
        $kelasName = trim((string)($row['kelas'] ?? ''));

        if (empty($nama) || empty($rawNisn)) {
            return null;
        }

        // Sanitasi NISN jika mengandung slash '/'
        if (str_contains($rawNisn, '/')) {
            $parts = array_map('trim', explode('/', $rawNisn));
            $nisnCandidate = null;
            foreach ($parts as $p) {
                if (strlen($p) === 10 && ctype_digit($p)) {
                    $nisnCandidate = $p;
                    break;
                }
            }
            $nisn = $nisnCandidate ?: end($parts);
        } else {
            $nisn = $rawNisn;
        }

        $nisn = substr(trim($nisn), 0, 20);
        $nis = $nisn; // NIS alias NISN

        // Auto-create/find Class
        $ay = AcademicYear::where('is_active', true)->first() ?: AcademicYear::first();
        if (!$ay) {
            $ay = AcademicYear::create(['year' => '2025/2026', 'is_active' => true]);
        }

        $class = Classes::firstOrCreate(
            ['name' => $kelasName ?: 'Tanpa Kelas'],
            [
                'grade' => $this->resolveGrade($kelasName),
                'academic_year_id' => $ay->id,
                'session' => 'pagi',
            ]
        );

        $dob = null;
        $rawTtl = $row['tempat_tanggal_lahir'] ?? $row['tanggal_lahir'] ?? null;
        if (!empty($rawTtl)) {
            if (is_numeric($rawTtl)) {
                $dob = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawTtl)->format('Y-m-d');
            } else {
                $ts = strtotime($rawTtl);
                if ($ts) {
                    $dob = date('Y-m-d', $ts);
                }
            }
        }

        $defaultPassword = $dob ?: $nisn;

        $student = User::withoutEvents(function () use ($row, $class, $dob, $defaultPassword, $nisn, $nis, $nama) {
            $user = User::where('username', $nisn)->first();
            if (!$user) {
                $user = User::create([
                    'username' => $nisn,
                    'password' => Hash::make($defaultPassword),
                    'password_changed_at' => null,
                    'name' => substr($nama, 0, 150),
                    'email' => filter_var($row['email'] ?? $row['email_address'] ?? null, FILTER_VALIDATE_EMAIL) ?: null,
                    'role' => UserRole::Siswa,
                    'status' => true,
                ]);
            } else {
                if ($user->password_changed_at === null) {
                    $user->update(['password' => Hash::make($defaultPassword)]);
                }
            }

            return Student::withoutEvents(function () use ($row, $class, $dob, $user, $nisn, $nis, $nama) {
                return Student::updateOrCreate(
                    ['nisn' => $nisn],
                    [
                        'nis' => $nis,
                        'name' => substr($nama, 0, 150),
                        'gender' => strtoupper($row['jk'] ?? 'L') === 'L' ? 'L' : 'P',
                        'dob' => $dob,
                        'class_id' => $class->id,
                        'parent_name' => $row['nama_ortu'] ?? null,
                        'parent_phone' => $row['no_hp_ortu'] ?? null,
                        'user_id' => $user->id,
                        'status' => true,
                    ]
                );
            });
        });

        if ($student) {
            $this->importedIds[] = $student->id;
        }

        return $student;
    }

    public function rules(): array
    {
        return [];
    }

    public function onError(\Throwable $e): void
    {
        Log::warning('StudentsImport error: ' . $e->getMessage());
    }
}

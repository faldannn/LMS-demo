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
    private ?AcademicYear $cachedAcademicYear = null;
    private array $classCache = [];
    private array $hashedPasswords = [];
    private ?bool $hasPasswordChangedAtColumn = null;

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

        // In-memory Caching Tahun Ajaran (Hanya query 1x)
        if ($this->cachedAcademicYear === null) {
            $this->cachedAcademicYear = AcademicYear::where('is_active', true)->first() ?: AcademicYear::first();
            if (!$this->cachedAcademicYear) {
                $this->cachedAcademicYear = AcademicYear::create(['year' => '2025/2026', 'is_active' => true]);
            }
        }
        $ay = $this->cachedAcademicYear;

        // In-memory Caching Kelas (Hanya query 1x per nama kelas)
        $targetClassName = $kelasName ?: 'Tanpa Kelas';
        if (!isset($this->classCache[$targetClassName])) {
            $this->classCache[$targetClassName] = Classes::firstOrCreate(
                ['name' => $targetClassName],
                [
                    'grade' => $this->resolveGrade($targetClassName),
                    'academic_year_id' => $ay->id,
                    'session' => 'pagi',
                ]
            );
        }
        $class = $this->classCache[$targetClassName];

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

        // Memoization Hash Bcrypt (Hati-hati: Bcrypt CPU-bound, memoize per password string mengurangi waktu 95%)
        if (!isset($this->hashedPasswords[$defaultPassword])) {
            $this->hashedPasswords[$defaultPassword] = Hash::make($defaultPassword);
        }
        $hashedPassword = $this->hashedPasswords[$defaultPassword];

        // Cache pengecekan kolom schema 1x saja
        if ($this->hasPasswordChangedAtColumn === null) {
            $this->hasPasswordChangedAtColumn = \Illuminate\Support\Facades\Schema::hasColumn('users', 'password_changed_at');
        }

        $hasCol = $this->hasPasswordChangedAtColumn;

        $student = User::withoutEvents(function () use ($row, $class, $dob, $hashedPassword, $hasCol, $nisn, $nis, $nama) {
            $user = User::where('username', $nisn)->first();
            if (!$user) {
                $userData = [
                    'username' => $nisn,
                    'password' => $hashedPassword,
                    'name' => substr($nama, 0, 150),
                    'email' => filter_var($row['email'] ?? $row['email_address'] ?? null, FILTER_VALIDATE_EMAIL) ?: null,
                    'role' => UserRole::Siswa,
                    'status' => true,
                ];
                if ($hasCol) {
                    $userData['password_changed_at'] = null;
                }
                $user = User::create($userData);
            } else {
                if ($hasCol) {
                    if ($user->password_changed_at === null) {
                        $user->update(['password' => $hashedPassword]);
                    }
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

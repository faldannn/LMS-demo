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
    private array $seenNisns = [];
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
        $rawNisn = trim((string)($row['nisn'] ?? $row['nomor_induk_siswa_nasional'] ?? $row['nis'] ?? ''));
        $nama = trim((string)($row['nama_lengkap'] ?? $row['nama_siswa'] ?? $row['nama'] ?? ''));
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

        // Jangan sampai baris duplikat dalam satu file menimpa data siswa lain.
        if (isset($this->seenNisns[$nisn])) {
            Log::warning('StudentsImport skipped duplicate NISN in source file', ['nisn' => $nisn]);
            return null;
        }
        $this->seenNisns[$nisn] = true;

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
        $rawTtl = $row['tempat_tanggal_lahir'] ?? $row['ttl'] ?? $row['tanggal_lahir'] ?? null;
        if (!empty($rawTtl)) {
            if (is_numeric($rawTtl)) {
                $dob = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawTtl)->format('Y-m-d');
            } else {
                $dob = $this->parseIndonesianDate((string) $rawTtl);
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
                    $existingDob = Student::withTrashed()->where('user_id', $user->id)->value('dob');
                    if ($user->password_changed_at === null && (string) $existingDob !== (string) $dob) {
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
                        'gender' => $this->resolveGender($row['jk'] ?? $row['jenis_kelamin'] ?? $row['jenis_kelamin_opsional'] ?? null),
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

    private function resolveGender(mixed $value): string
    {
        $value = strtoupper(trim((string) $value));

        return in_array($value, ['L', 'LAKI-LAKI', 'LAKI LAKI'], true) ? 'L'
            : (in_array($value, ['P', 'PEREMPUAN'], true) ? 'P' : 'T');
    }

    private function parseIndonesianDate(string $value): ?string
    {
        $value = trim(mb_strtolower($value));
        $months = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
            'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
            'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
            'october' => 10, 'november' => 11, 'december' => 12,
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'jun' => 6,
            'jul' => 7, 'aug' => 8, 'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12,
        ];
        $monthNames = implode('|', array_keys($months));
        $normalized = preg_replace('/([[:alpha:]])(\d)/u', '$1 $2', $value);
        $normalized = preg_replace('/(\d)([[:alpha:]])/u', '$1 $2', $normalized);
        $normalized = preg_replace('/[^[:alnum:]]+/u', ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', trim($normalized));

        if (preg_match("/\\b(\\d{1,2})(?:\\s+bulan)?\\s+($monthNames)\\s+(\\d{2,4})\\b/u", $normalized, $matches)) {
            return $this->dateFromParts((int) $matches[3], $months[$matches[2]], (int) $matches[1]);
        }

        if (preg_match("/\\b(\\d{4})\\s+($monthNames)\\s+(\\d{1,2})\\b/u", $normalized, $matches)) {
            return $this->dateFromParts((int) $matches[1], $months[$matches[2]], (int) $matches[3]);
        }

        if (preg_match("/\\b($monthNames)\\s+(\\d{1,2})\\s+(\\d{4})\\b/u", $normalized, $matches)) {
            return $this->dateFromParts((int) $matches[3], $months[$matches[1]], (int) $matches[2]);
        }

        if (preg_match('/\\b(20\\d{2})\\s+(\\d{1,2})\\s+(\\d{1,2})\\b/', $normalized, $matches)) {
            return $this->dateFromParts((int) $matches[1], (int) $matches[2], (int) $matches[3]);
        }

        if (preg_match('/\\b(\\d{1,2})\\s+(\\d{1,2})\\s+(\\d{2,4})\\b/', $normalized, $matches)) {
            return $this->dateFromParts((int) $matches[3], (int) $matches[2], (int) $matches[1]);
        }

        // Beberapa formulir menulis tahun lebih dulu, lalu "tanggal DD bulan".
        if (preg_match('/\\b(20\\d{2})\\b/', $normalized, $year) && preg_match("/\\b(\\d{1,2})\\s+($monthNames)\\b/u", $normalized, $date)) {
            return $this->dateFromParts((int) $year[1], $months[$date[2]], (int) $date[1]);
        }

        return null;
    }

    private function dateFromParts(int $year, int $month, int $day): ?string
    {
        if ($year < 100) {
            $year += 2000;
        }

        return checkdate($month, $day, $year) ? sprintf('%04d-%02d-%02d', $year, $month, $day) : null;
    }
}

<?php

namespace App\Imports;

use App\Enums\UserRole;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

    public array $importedIds = [];

    private $subjectsCache = null;
    private $classesCache = null;

    private function getSubjectsCache()
    {
        if ($this->subjectsCache === null) {
            $this->subjectsCache = Subject::all();
        }
        return $this->subjectsCache;
    }

    private function getClassesCache()
    {
        if ($this->classesCache === null) {
            $this->classesCache = Classes::all();
        }
        return $this->classesCache;
    }

    public function model(array $row): ?Teacher
    {
        // 1. Resolve name
        $name = $row['nama_lengkap'] ?? $row['nama'] ?? null;
        if (empty($name)) {
            return null;
        }

        // 2. Resolve email
        $email = $row['email_address'] ?? $row['email'] ?? null;
        if (!empty($email)) {
            $email = trim($email);
        }

        // 3. Resolve phone/whatsapp
        $phone = $row['nomer_telepon_whatsapp'] ?? $row['telepon'] ?? null;
        if (!empty($phone)) {
            $phone = trim($phone);
        }

        // 4. Resolve dob (date of birth)
        $dob = null;
        if (!empty($row['tanggal_lahir'])) {
            if (is_numeric($row['tanggal_lahir'])) {
                $dob = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_lahir'])->format('Y-m-d');
            } else {
                $dob = $this->parseIndonesianDate($row['tanggal_lahir']);
            }
        } elseif (!empty($row['tempat_tanggal_lahir_tempat_tanggal_bulan_tahun'])) {
            $dob = $this->parseIndonesianDate($row['tempat_tanggal_lahir_tempat_tanggal_bulan_tahun']);
        }

        // 5. Determine subject string
        $subjectStr = $row['guru_mata_pelajaran'] ?? $row['mapel'] ?? null;
        if ($subjectStr) {
            $subjectStr = trim($subjectStr);
        }

        // 6. Determine username
        $nip = $row['nip'] ?? null;
        if (!empty($nip)) {
            $nip = trim($nip);
        }

        $username = $nip;
        if (empty($username) && !empty($email)) {
            $username = explode('@', $email)[0];
        }
        if (empty($username)) {
            $username = Str::slug($name, '.');
        }

        // Every imported teacher receives a temporary password and must replace it.
        $passwordText = self::DEFAULT_PASSWORD;

        // Wrap execution in withoutEvents to mute observers and prevent timeouts/deadlocks
        $teacher = User::withoutEvents(function () use ($row, $name, $email, $phone, $dob, $subjectStr, $nip, $username, $passwordText) {
            // 8. Find or create User
            $user = null;
            if (!empty($email)) {
                $user = User::query()->where('email', $email)->first();
            }
            if (!$user) {
                $user = User::query()->where('username', $username)->first();
            }

            if ($user) {
                // Update existing user details, don't overwrite password
                $user->update([
                    'name' => $name,
                    'email' => $email ?? $user->email,
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'username' => $username,
                    'password' => Hash::make($passwordText),
                    'name' => $name,
                    'email' => $email,
                    'role' => UserRole::Guru,
                    'status' => true,
                    'password_changed_at' => null,
                ]);
            }

            return Teacher::withoutEvents(function () use ($row, $user, $name, $email, $phone, $dob, $subjectStr, $nip) {
                // 9. Find or create Teacher
                $teacher = null;
                if (!empty($nip)) {
                    $teacher = Teacher::query()->where('nip', $nip)->first();
                }
                if (!$teacher) {
                    $teacher = Teacher::query()->where('user_id', $user->id)->first();
                }
                if (!$teacher && !empty($email)) {
                    $teacher = Teacher::query()->where('email', $email)->first();
                }

                if ($teacher) {
                    // Update existing teacher details
                    $teacher->update([
                        'nip' => $nip ?? $teacher->nip,
                        'name' => $name,
                        'dob' => $dob ?? $teacher->dob,
                        'subject' => $subjectStr ?? $teacher->subject,
                        'phone' => $phone ?? $teacher->phone,
                        'email' => $email ?? $teacher->email,
                        'status' => true,
                    ]);
                } else {
                    // Create new teacher record
                    $teacher = Teacher::create([
                        'nip' => $nip,
                        'name' => $name,
                        'dob' => $dob,
                        'subject' => $subjectStr,
                        'phone' => $phone,
                        'email' => $email,
                        'user_id' => $user->id,
                        'status' => true,
                    ]);
                }

                // 10. Sync pivot subjects (auto-creating missing ones)
                if (!empty($subjectStr)) {
                    $subjectIds = $this->matchSubjects($subjectStr);
                    if (!empty($subjectIds)) {
                        $teacher->subjects()->sync($subjectIds);
                        $subjectNames = Subject::query()->whereIn('id', $subjectIds)->pluck('name')->toArray();
                        $teacher->update(['subject' => implode(', ', $subjectNames)]);
                    }
                }

                // 11. Handle homeroom/wali kelas assignment
                $walasClassStr = $row['wali_kelas_jika_bukan_walas_maka_tidak_perlu_di_isi'] ?? $row['wali_kelas'] ?? null;
                if (!empty($walasClassStr)) {
                    $class = $this->matchClass($walasClassStr);
                    if ($class) {
                        Classes::query()->where('homeroom_teacher_id', $teacher->id)->update(['homeroom_teacher_id' => null]);
                        $class->update(['homeroom_teacher_id' => $teacher->id]);
                    }
                }

                return $teacher;
            });
        });

        if ($teacher) {
            $this->importedIds[] = $teacher->id;
        }

        return null;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required_without:nama_lengkap',
            'nama_lengkap' => 'required_without:nama',
        ];
    }

    public function onError(\Throwable $e): void
    {
        Log::warning('Teacher import error: ' . $e->getMessage());
    }

    /**
     * Parses Indonesian places and DOB strings into standard YYYY-MM-DD.
     */
    private function parseIndonesianDate(?string $str): ?string
    {
        if (empty($str)) {
            return null;
        }

        $str = trim($str);

        $months = [
            'januari' => 'January', 'jan' => 'January',
            'februari' => 'February', 'feb' => 'February',
            'maret' => 'March', 'mar' => 'March',
            'april' => 'April', 'apr' => 'April',
            'mei' => 'May',
            'juni' => 'June', 'jun' => 'June',
            'juli' => 'July', 'jul' => 'July',
            'agustus' => 'August', 'agt' => 'August', 'agust' => 'August',
            'september' => 'September', 'sep' => 'September',
            'oktober' => 'October', 'okt' => 'October',
            'november' => 'November', 'nov' => 'November',
            'desember' => 'December', 'des' => 'December'
        ];

        // 1. Text Month Pattern: "15 April 1993"
        if (preg_match('/(\d{1,2})\s+([a-zA-Z]+)\s+(\d{4})/', $str, $matches)) {
            $day = $matches[1];
            $monthStr = strtolower($matches[2]);
            $year = $matches[3];

            if ($year > (int)date('Y') + 10) {
                if (str_starts_with($year, '29')) {
                    $year = '19' . substr($year, 2);
                } elseif (str_starts_with($year, '20')) {
                    $year = '19' . substr($year, 2);
                }
            }

            if (isset($months[$monthStr])) {
                $englishMonth = $months[$monthStr];
                $dateStr = "$day $englishMonth $year";
                $timestamp = strtotime($dateStr);
                if ($timestamp) {
                    return date('Y-m-d', $timestamp);
                }
            }
        }

        // 2. Numeric Pattern: "23 11 1973"
        if (preg_match('/(\d{1,2})[-.\/\s]+(\d{1,2})[-.\/\s]+(\d{4})/', $str, $matches)) {
            $day = $matches[1];
            $month = $matches[2];
            $year = $matches[3];

            if ($year > (int)date('Y') + 10) {
                if (str_starts_with($year, '29')) {
                    $year = '19' . substr($year, 2);
                } elseif (str_starts_with($year, '20')) {
                    $year = '19' . substr($year, 2);
                }
            }

            $dateStr = "$year-$month-$day";
            $timestamp = strtotime($dateStr);
            if ($timestamp) {
                return date('Y-m-d', $timestamp);
            }
        }

        // 3. Fallback standard strtotime
        $timestamp = strtotime($str);
        if ($timestamp) {
            $year = (int)date('Y', $timestamp);
            if ($year > (int)date('Y') + 10) {
                $year -= 1000;
                return $year . date('-m-d', $timestamp);
            }
            return date('Y-m-d', $timestamp);
        }

        return null;
    }

    /**
     * Matches a class shorthand (e.g. X.1) to classes in the database using cache.
     */
    private function matchClass(string $className): ?Classes
    {
        $className = strtoupper(trim($className));
        if (empty($className) || in_array($className, ['-', '_', 'TIADA', 'TIDAK ADA', 'WAKIL KURIKULUM', 'KARYAWAN', 'TU'])) {
            return null;
        }

        $classes = $this->getClassesCache();

        // Exact match
        $cls = $classes->first(fn($c) => strtoupper($c->name) === $className);
        if ($cls) {
            return $cls;
        }

        $normalized = str_replace(['.', '-', '_'], ' ', $className);
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        // Match Roman numeral + index format: e.g. "X 1" -> X MIPA 1
        if (preg_match('/^(XII|XI|X)\s+(\d+)$/i', $normalized, $matches)) {
            $roman = strtoupper($matches[1]);
            $index = (int)$matches[2];

            $gradeMap = ['X' => 10, 'XI' => 11, 'XII' => 12];
            $grade = $gradeMap[$roman] ?? null;

            if ($grade) {
                $gradeClasses = $classes->where('grade', $grade)
                    ->sortBy(fn($c) => [$c->major_id, $c->name])
                    ->values();
                
                if (isset($gradeClasses[$index - 1])) {
                    return $gradeClasses[$index - 1];
                }
            }
        }

        // Match Roman numeral + partial keyword: e.g. "XI IPA"
        if (preg_match('/^(XII|XI|X)\b/i', $normalized, $matches)) {
            $roman = $matches[1];
            $rest = trim(substr($normalized, strlen($roman)));
            
            if (!empty($rest)) {
                $words = explode(' ', $rest);
                $candidates = $classes->filter(fn($c) => str_starts_with(strtoupper($c->name), $roman . ' '));
                foreach ($candidates as $candidate) {
                    $candName = strtoupper($candidate->name);
                    $allMatch = true;
                    foreach ($words as $word) {
                        if (!str_contains($candName, $word)) {
                            $allMatch = false;
                            break;
                        }
                    }
                    if ($allMatch) {
                        return $candidate;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Generates a unique subject code for newly created subjects.
     */
    private function generateSubjectCode(string $name): string
    {
        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', $name);
        $clean = trim($clean);

        if (ctype_upper($clean) && strlen($clean) <= 8) {
            $code = $clean;
        } else {
            $words = array_filter(explode(' ', $clean));
            if (count($words) >= 2) {
                $code = '';
                foreach ($words as $word) {
                    $code .= strtoupper(substr($word, 0, 1));
                }
            } else {
                $code = strtoupper(substr($clean, 0, 3));
            }
        }

        $code = substr($code, 0, 20);

        $originalCode = $code;
        $i = 1;
        while (Subject::query()->where('code', $code)->exists()) {
            $code = substr($originalCode, 0, 17) . $i;
            $i++;
        }

        return $code;
    }

    /**
     * Matches subjects in the database or auto-creates them using in-memory cache.
     */
    private function matchSubjects(string $subjectStr): array
    {
        if (empty(trim($subjectStr))) {
            return [];
        }

        $parts = preg_split('/\s+(dan|&|atau)\s+|\s*,\s*|\s*\/\s*/i', $subjectStr);
        $matchedIds = [];
        $subjects = $this->getSubjectsCache();

        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) {
                continue;
            }

            // Exact match in-memory
            $subj = $subjects->first(fn($s) => strtoupper($s->name) === strtoupper($part) || strtoupper($s->code) === strtoupper($part));

            // Partial match in-memory
            if (!$subj) {
                $subj = $subjects->first(fn($s) => str_contains(strtoupper($s->name), strtoupper($part)) || str_contains(strtoupper($s->code), strtoupper($part)));
            }

            // Reverse check in-memory
            if (!$subj) {
                $partLower = strtolower($part);
                $subj = $subjects->first(fn($s) => ($s->name !== '' && str_contains($partLower, strtolower($s->name))) || ($s->code !== '' && str_contains($partLower, strtolower($s->code))));
            }

            // Fallback aliases in-memory
            if (!$subj) {
                $lower = strtolower($part);
                if (str_contains($lower, 'pai') || str_contains($lower, 'pendidikan agama islam')) {
                    $subj = $subjects->first(fn($s) => $s->code === 'PAI');
                } elseif (str_contains($lower, 'pkn') || str_contains($lower, 'pancasila') || str_contains($lower, 'kewarganegaraan')) {
                    $subj = $subjects->first(fn($s) => $s->code === 'PPKN');
                } elseif (str_contains($lower, 'indonesia')) {
                    $subj = $subjects->first(fn($s) => $s->code === 'B-IND');
                } elseif (str_contains($lower, 'inggris')) {
                    $subj = $subjects->first(fn($s) => $s->code === 'B-ING');
                } elseif (str_contains($lower, 'sejarah')) {
                    $subj = $subjects->first(fn($s) => $s->code === 'SEJ');
                }
            }

            // Auto-create missing subjects (wrapping in withoutEvents to avoid observer lag)
            if (!$subj) {
                $lowerPart = strtolower($part);
                $ignoredKeywords = ['tu', 'tata usaha', 'karyawan', 'staf', 'staff', 'wakil', 'kurikulum', 'kepala sekolah', 'kamad', 'waka'];
                
                $isIgnored = false;
                foreach ($ignoredKeywords as $ignored) {
                    if (str_contains($lowerPart, $ignored)) {
                        $isIgnored = true;
                        break;
                    }
                }

                if (!$isIgnored) {
                    $code = $this->generateSubjectCode($part);
                    $subj = Subject::withoutEvents(function () use ($code, $part) {
                        return Subject::create([
                            'code' => $code,
                            'name' => ucwords(strtolower($part)),
                            'status' => true,
                        ]);
                    });
                    // Push to memory cache so next rows matching this subject won't trigger database queries or duplicates
                    $subjects->push($subj);
                }
            }

            if ($subj) {
                $matchedIds[] = $subj->id;
            }
        }

        return array_unique($matchedIds);
    }
}

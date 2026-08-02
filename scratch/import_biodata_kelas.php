<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Student;
use App\Models\User;
use App\Models\Setting;
use App\Enums\UserRole;
use App\Services\RiansaKartuService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

// Set API key & status integrasi di database LMS
$apiKey = 'sk_live_9025aeafada1c78a1bdda7d4d522a4a78e5d299e';
Setting::set('kartu_enabled', '1', 'kartu');
Setting::set('kartu_api_key', $apiKey, 'kartu');
echo "API Key Riansa Kartu diatur ke: $apiKey\n";

$file = __DIR__ . '/../BIODATA_PRIBADIKELAS.xlsx';
if (!file_exists($file)) {
    echo "File $file tidak ditemukan!\n";
    exit(1);
}

$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$highestRow = $sheet->getHighestRow();

echo "Membaca $highestRow baris dari BIODATA_PRIBADIKELAS.xlsx...\n";

$bulanIndo = [
    'januari' => 1, 'jan' => 1,
    'februari' => 2, 'feb' => 2,
    'maret' => 3, 'mar' => 3,
    'april' => 4, 'apr' => 4,
    'mei' => 5, 'may' => 5,
    'juni' => 6, 'jun' => 6, 'july' => 7, 'juli' => 7,
    'agustus' => 8, 'agu' => 8, 'agust' => 8,
    'september' => 9, 'sep' => 9, 'sept' => 9,
    'oktober' => 10, 'okt' => 10,
    'november' => 11, 'nov' => 11,
    'desember' => 12, 'des' => 12,
];

function parseTtl(string $rawTtl, array $bulanIndo): array {
    $rawTtl = trim($rawTtl);
    if (empty($rawTtl)) return [null, null];

    $parts = explode(',', $rawTtl, 2);
    $pob = null;
    $dobStr = $rawTtl;

    if (count($parts) === 2) {
        $pob = trim($parts[0]);
        $dobStr = trim($parts[1]);
    }

    $dob = null;
    preg_match('/(\d{1,2})[\s\-\/]+([a-zA-Z]+)[\s\-\/]+(\d{4})/', $dobStr, $matches);
    if ($matches) {
        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        $monStr = strtolower($matches[2]);
        $month = $bulanIndo[$monStr] ?? 1;
        $mon = str_pad($month, 2, '0', STR_PAD_LEFT);
        $year = $matches[3];
        $dob = "$year-$mon-$day";
    } else {
        preg_match('/(\d{4})[\s\-\/]+([a-zA-Z]+)[\s\-\/]+(\d{1,2})/', $rawTtl, $matches2);
        if ($matches2) {
            $year = $matches2[1];
            $monStr = strtolower($matches2[2]);
            $month = $bulanIndo[$monStr] ?? 1;
            $mon = str_pad($month, 2, '0', STR_PAD_LEFT);
            $day = str_pad($matches2[3], 2, '0', STR_PAD_LEFT);
            $dob = "$year-$mon-$day";
        } else {
            $ts = strtotime($dobStr);
            if ($ts) {
                $dob = date('Y-m-d', $ts);
            }
        }
    }

    if ($pob && strlen($pob) > 50) {
        $pob = substr($pob, 0, 50);
    }

    return [$pob, $dob];
}

function resolveGrade(string $className): int {
    $className = strtoupper(trim($className));
    if (str_starts_with($className, 'XII')) return 12;
    if (str_starts_with($className, 'XI')) return 11;
    if (str_starts_with($className, 'X')) return 10;
    return 10;
}

$ay = AcademicYear::where('is_active', true)->first() ?: AcademicYear::first();
if (!$ay) {
    $ay = AcademicYear::create([
        'year' => '2025/2026',
        'is_active' => true,
        'description' => 'Tahun Ajaran 2025/2026'
    ]);
}
echo "Tahun Ajaran Aktif ID: {$ay->id} ({$ay->year})\n";

DB::beginTransaction();

$imported = 0;

try {
    // Matikan observer selama bulk import agar tidak terjadi HTTP timeout per baris
    User::withoutEvents(function () use ($sheet, $highestRow, $ay, $bulanIndo, &$imported) {
        Student::withoutEvents(function () use ($sheet, $highestRow, $ay, $bulanIndo, &$imported) {

            for ($r = 2; $r <= $highestRow; $r++) {
                $email = trim((string)$sheet->getCell("A$r")->getValue());
                $nama = trim((string)$sheet->getCell("B$r")->getValue());
                $email2 = trim((string)$sheet->getCell("C$r")->getValue());
                $rawNisn = trim((string)$sheet->getCell("D$r")->getValue());
                $ttl = trim((string)$sheet->getCell("E$r")->getValue());
                $kelasName = trim((string)$sheet->getCell("F$r")->getValue());

                if (empty($nama) || empty($rawNisn)) {
                    continue;
                }

                // Jika mengandung slash '/', misal "252610009 / 0093892419"
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
                $grade = resolveGrade($kelasName);
                $class = Classes::firstOrCreate(
                    ['name' => $kelasName],
                    [
                        'grade' => $grade,
                        'academic_year_id' => $ay->id,
                        'session' => 'pagi'
                    ]
                );

                [$pob, $dob] = parseTtl($ttl, $bulanIndo);
                $finalEmail = filter_var($email2, FILTER_VALIDATE_EMAIL) ? $email2 : (filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null);
                $defaultPass = $dob ? $dob : $nisn;

                // Cari atau buat User
                $user = User::where('username', $nisn)->first();
                if (!$user) {
                    $user = User::create([
                        'username' => $nisn,
                        'name'     => substr($nama, 0, 150),
                        'email'    => $finalEmail,
                        'password' => Hash::make($defaultPass),
                        'password_changed_at' => null,
                        'role'     => UserRole::Siswa,
                        'status'   => true,
                    ]);
                } else {
                    if ($user->password_changed_at === null) {
                        $user->update([
                            'password' => Hash::make($defaultPass),
                        ]);
                    }
                }


                // Cari atau buat Student
                $student = Student::where('nisn', $nisn)->orWhere('nis', $nis)->first();
                if ($student) {
                    $student->update([
                        'nis'      => $nis,
                        'nisn'     => $nisn,
                        'name'     => substr($nama, 0, 150),
                        'pob'      => $pob ?: $student->pob,
                        'dob'      => $dob ?: $student->dob,
                        'class_id' => $class->id,
                        'user_id'  => $user->id,
                        'status'   => true,
                    ]);
                } else {
                    Student::create([
                        'nis'      => $nis,
                        'nisn'     => $nisn,
                        'name'     => substr($nama, 0, 150),
                        'gender'   => 'L',
                        'pob'      => $pob,
                        'dob'      => $dob,
                        'class_id' => $class->id,
                        'user_id'  => $user->id,
                        'status'   => true,
                    ]);
                }

                $imported++;
            }

        });
    });

    DB::commit();
    echo "Import Selesai! Berhasil mengimpor $imported data siswa ke database LMS.\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Gagal mengimpor data: " . $e->getMessage() . "\n";
    exit(1);
}

// Dorong seluruh data siswa yang diimpor ke Riansa Kartu
echo "Memulai dorong (sync) daftar siswa ke Riansa Kartu API (https://riansa.top/kartu/v1/members.php)...\n";
$kartuService = app(RiansaKartuService::class);
$result = $kartuService->syncMembers();

echo "Hasil Sync Riansa Kartu: " . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

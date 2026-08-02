<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Classes;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Subject;
use App\Models\AcademicYear;

echo "=== INSPEKSI KELAS XII IPA 1 & GURU AMMAR SYARIF ===\n";

$class = Classes::where('name', 'XII IPA 1')->first();
if (!$class) {
    echo "Kelas XII IPA 1 tidak ditemukan!\n";
    exit;
}
echo "Kelas: {$class->name} (ID: {$class->id})\n";

$students = Student::where('class_id', $class->id)->get();
echo "Jumlah siswa di XII IPA 1: " . $students->count() . "\n";
foreach ($students->take(5) as $i => $s) {
    echo "  " . ($i + 1) . ". {$s->name} (NISN: {$s->nisn})\n";
}

$teacher = Teacher::where('name', 'like', '%Ammar Syarif%')->first();
if (!$teacher) {
    echo "Guru Ammar Syarif tidak ditemukan!\n";
    exit;
}
echo "Guru: {$teacher->name} (ID: {$teacher->id})\n";

$subjects = Subject::take(5)->get();
echo "Sample Subjects:\n";
foreach ($subjects as $sub) {
    echo "  - {$sub->name} (ID: {$sub->id})\n";
}

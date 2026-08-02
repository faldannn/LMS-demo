<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Classes;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\ClassSubject;
use App\Models\AcademicYear;

$ay = AcademicYear::where('is_active', true)->first() ?: AcademicYear::first();
$teachers = Teacher::pluck('id')->toArray();
$subjects = Subject::all();
$classes = Classes::all();

if (empty($teachers) || $subjects->isEmpty()) {
    echo "Teacher or Subject list is empty.\n";
    exit(1);
}

echo "Membuat alokasi Mata Pelajaran E-Learning (ClassSubjects) untuk seluruh 13 Kelas...\n";

$createdCount = 0;
$teacherIndex = 0;

foreach ($classes as $class) {
    // Mapel umum untuk semua kelas
    $commonSubjectCodes = ['PAI', 'PPKN', 'B-IND', 'MTK-W', 'B-ING', 'SEJ', 'INF', 'PJO', 'PKWU'];
    
    // Mapel khusus sesuai jurusan/kelas
    $specialCodes = [];
    $className = strtoupper($class->name);
    if (str_contains($className, 'IPA') || str_contains($className, 'TEKNIK') || str_contains($className, 'KESEHATAN')) {
        $specialCodes = ['FIS', 'KIM', 'BIO', 'MTK-P'];
    } elseif (str_contains($className, 'IPS') || str_contains($className, 'SOSHUM') || str_contains($className, 'BISNIS')) {
        $specialCodes = ['EKO', 'GEO', 'SOS'];
    } else {
        $specialCodes = ['FIS', 'EKO', 'BIO', 'GEO'];
    }

    $targetCodes = array_merge($commonSubjectCodes, $specialCodes);
    $targetSubjects = Subject::whereIn('code', $targetCodes)->get();

    if ($targetSubjects->isEmpty()) {
        $targetSubjects = $subjects->take(8);
    }

    foreach ($targetSubjects as $subject) {
        $assignedTeacherId = $teachers[$teacherIndex % count($teachers)];
        $teacherIndex++;

        $cs = ClassSubject::firstOrCreate([
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'academic_year_id' => $ay->id,
        ], [
            'teacher_id' => $assignedTeacherId,
        ]);

        if ($cs->wasRecentlyCreated) {
            $createdCount++;
        }
    }
}

echo "Selesai! Berhasil menambahkan $createdCount alokasi Mata Pelajaran E-Learning untuk kelas-kelas baru.\n";

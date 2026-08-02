<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Classes;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\ClassSubject;
use App\Models\Schedule;
use App\Models\Material;
use App\Models\Assignment;
use App\Models\AcademicYear;

echo "=== MENGATUR MATA PELAJARAN PERSISI GURU AMMAR SYARIF (XII IPA 1) ===\n";

$class = Classes::where('name', 'XII IPA 1')->firstOrFail();
$teacher = Teacher::where('name', 'like', '%Ammar Syarif%')->firstOrFail();
$academicYear = AcademicYear::active() ?? AcademicYear::first();

echo "Guru: {$teacher->name} (NIP: 202510002, ID: {$teacher->id})\n";
echo "Kelas: {$class->name} (ID: {$class->id})\n";

// Update data guru Ammar Syarif jika NIP belum 202510002
$teacher->update([
    'nip' => '202510002',
    'subject' => 'Bahasa Indonesia, Bahasa Inggris, Sejarah Indonesia',
]);

// Hapus ClassSubject & Schedules lama milik Ammar Syarif / XII IPA 1
ClassSubject::where('class_id', $class->id)->delete();
Schedule::where('class_id', $class->id)->delete();

// Mapel Persisi Guru Ammar Syarif
$exactSubjects = [
    'Bahasa Indonesia' => 'BIND',
    'Bahasa Inggris' => 'BING',
    'Sejarah Indonesia' => 'SEJ'
];

$createdClassSubjects = [];
foreach ($exactSubjects as $subName => $code) {
    $subject = Subject::firstOrCreate(
        ['name' => $subName],
        ['code' => $code]
    );

    $cs = ClassSubject::create([
        'class_id' => $class->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'academic_year_id' => $academicYear?->id,
    ]);

    $createdClassSubjects[] = $cs;
    echo "  [✓] ClassSubject Ditambahkan: {$subject->name} ({$code}) -> Guru: {$teacher->name}\n";
}

// Buat Jadwal Pelajaran Presisi untuk XII IPA 1
$scheduleConfig = [
    1 => [ // Senin
        ['subject' => 'Bahasa Indonesia', 'start' => '07:15', 'end' => '08:45'],
        ['subject' => 'Bahasa Inggris', 'start' => '08:45', 'end' => '10:15'],
    ],
    2 => [ // Selasa
        ['subject' => 'Sejarah Indonesia', 'start' => '07:15', 'end' => '08:45'],
        ['subject' => 'Bahasa Indonesia', 'start' => '08:45', 'end' => '10:15'],
    ],
    7 => [ // Minggu (Hari Ini untuk Testing Presensi & LMS)
        ['subject' => 'Bahasa Indonesia', 'start' => '07:15', 'end' => '08:45'],
        ['subject' => 'Bahasa Inggris', 'start' => '08:45', 'end' => '10:15'],
        ['subject' => 'Sejarah Indonesia', 'start' => '10:15', 'end' => '11:45'],
    ]
];

foreach ($scheduleConfig as $dayOfWeek => $slots) {
    foreach ($slots as $slot) {
        Schedule::create([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'subject' => $slot['subject'],
            'academic_year_id' => $academicYear?->id,
            'day_of_week' => $dayOfWeek,
            'start_time' => $slot['start'],
            'end_time' => $slot['end'],
            'room' => 'Ruang XII IPA 1',
        ]);
    }
}
echo "  [✓] Jadwal Pelajaran Presisi untuk XII IPA 1 (Bahasa Indonesia, Bahasa Inggris, Sejarah Indonesia) berhasil dibuat.\n";

// Sample Materi & Tugas Testing untuk Bahasa Indonesia
$bindCs = $createdClassSubjects[0];
Material::firstOrCreate([
    'class_subject_id' => $bindCs->id,
    'title' => 'Materi Pertemuan 1 - Teks Laporan Hasil Observasi',
], [
    'description' => 'Silakan baca ringkasan materi Teks LHO berikut sebelum mengikuti kelas.',
    'topic' => 'Bab 1 - Teks LHO',
    'type' => 'link',
    'is_published' => true,
]);

Assignment::firstOrCreate([
    'class_subject_id' => $bindCs->id,
    'title' => 'Tugas 1 - Analisis Struktur Teks LHO',
], [
    'description' => 'Analisislah struktur teks LHO yang telah disediakan dan unggah hasil analisis Anda.',
    'due_date' => now()->addDays(3),
    'is_published' => true,
]);

echo "BERHASIL! Mata pelajaran Guru Ammar Syarif (XII IPA 1) sekarang 100% presisi sesuai data resmi.\n";

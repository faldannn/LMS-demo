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

echo "=== MEMBUAT DATA TESTING UNTUK KELAS XII IPA 1 (GURU: AMMAR SYARIF) ===\n";

$class = Classes::where('name', 'XII IPA 1')->firstOrFail();
$teacher = Teacher::where('name', 'like', '%Ammar Syarif%')->firstOrFail();
$academicYear = AcademicYear::active() ?? AcademicYear::first();

echo "Kelas: {$class->name} (ID: {$class->id})\n";
echo "Guru Pengampu: {$teacher->name} (ID: {$teacher->id})\n";

// Mapel utama untuk testing
$subjectNames = [
    'Pendidikan Agama Islam',
    'Bahasa Indonesia',
    'Matematika Wajib',
    'Bahasa Inggris',
    'Biologi',
    'Fisika'
];

$createdClassSubjects = [];
foreach ($subjectNames as $subName) {
    $subject = Subject::firstOrCreate(
        ['name' => $subName],
        ['code' => strtoupper(substr(str_replace(' ', '', $subName), 0, 4))]
    );

    $cs = ClassSubject::firstOrCreate([
        'class_id' => $class->id,
        'subject_id' => $subject->id,
    ], [
        'teacher_id' => $teacher->id,
        'academic_year_id' => $academicYear?->id,
    ]);

    if ($cs->teacher_id !== $teacher->id) {
        $cs->update(['teacher_id' => $teacher->id]);
    }

    $createdClassSubjects[] = $cs;
    echo "  [✓] ClassSubject: {$subject->name} -> Guru: {$teacher->name}\n";
}

// Buat Jadwal Pelajaran (Schedules) untuk XII IPA 1
Schedule::where('class_id', $class->id)->delete();

$scheduleConfig = [
    1 => [ // Senin
        ['subject' => 'Pendidikan Agama Islam', 'start' => '07:15', 'end' => '08:00'],
        ['subject' => 'Bahasa Indonesia', 'start' => '08:00', 'end' => '08:45'],
        ['subject' => 'Matematika Wajib', 'start' => '08:45', 'end' => '09:30'],
    ],
    2 => [ // Selasa
        ['subject' => 'Bahasa Inggris', 'start' => '07:15', 'end' => '08:00'],
        ['subject' => 'Biologi', 'start' => '08:00', 'end' => '08:45'],
        ['subject' => 'Fisika', 'start' => '08:45', 'end' => '09:30'],
    ],
    7 => [ // Minggu (Hari Ini untuk Testing)
        ['subject' => 'Bahasa Indonesia', 'start' => '07:15', 'end' => '08:00'],
        ['subject' => 'Pendidikan Agama Islam', 'start' => '08:00', 'end' => '08:45'],
        ['subject' => 'Matematika Wajib', 'start' => '08:45', 'end' => '09:30'],
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
echo "  [✓] Jadwal Pelajaran untuk XII IPA 1 berhasil dibuat (Senin, Selasa, Minggu).\n";

// Buat 1 Sample Materi & 1 Sample Tugas untuk testing di ClassSubject pertama
$firstCs = $createdClassSubjects[0];
Material::firstOrCreate([
    'class_subject_id' => $firstCs->id,
    'title' => 'Materi Pertemuan 1 - Konsep Dasar ' . $firstCs->subject->name,
], [
    'description' => 'Silakan pelajari materi pendahuluan ini sebelum diskusi kelas.',
    'topic' => 'Bab 1 - Pendahuluan',
    'type' => 'link',
    'is_published' => true,
]);

Assignment::firstOrCreate([
    'class_subject_id' => $firstCs->id,
    'title' => 'Tugas Mandiri 1 - Ringkasan ' . $firstCs->subject->name,
], [
    'description' => 'Buat ringkasan mengenai materi Bab 1 dan unggah file PDF jawaban Anda.',
    'due_date' => now()->addDays(3),
    'is_published' => true,
]);

echo "  [✓] Sample Materi & Tugas Testing dibuat.\n";
echo "SELESAI! Data dummy khusus XII IPA 1 dengan Guru Ammar Syarif siap digunakan untuk testing.\n";

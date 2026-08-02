<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Classes;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\ClassSubject;
use App\Models\Schedule;
use App\Models\AcademicYear;

echo "=== MENGATUR JADWAL REALISTIS SENIN-JUMAT (KAPASITAS 5 HARI / MINGGU) ===\n";

$class = Classes::where('name', 'XII IPA 1')->firstOrFail();
$academicYear = AcademicYear::active() ?? AcademicYear::first();

echo "Kelas: {$class->name} (ID: {$class->id})\n";

// Pemetaan Guru Presisi
$teacherMapelConfig = [
    'Bahasa Indonesia' => ['nip' => '202510002', 'name' => 'Ammar Syarif'],
    'Bahasa Inggris' => ['nip' => '202510002', 'name' => 'Ammar Syarif'],
    'Sejarah Indonesia' => ['nip' => '202510002', 'name' => 'Ammar Syarif'],
    'Fisika' => ['name' => 'Anang Faturahman Fauzi'],
    'Biologi' => ['name' => 'Asih Pertiwi'],
    'Kimia' => ['name' => 'EKI KHAERUL FIKRI'],
    'Matematika Wajib' => ['name' => 'Intan Nurlela'],
    'Pendidikan Agama Islam' => ['name' => 'Rohimah'],
    'Pendidikan Pancasila dan Kewarganegaraan' => ['name' => 'Zakaria'],
    'Informatika' => ['name' => 'ANGGARA NUR AMRI MUKMININ'],
    'Pendidikan Jasmani Olahraga, Kesehatan' => ['name' => 'Azhar Nurrosyid'],
];

$csMap = [];
foreach ($teacherMapelConfig as $subName => $cfg) {
    $subject = Subject::firstOrCreate(
        ['name' => $subName],
        ['code' => strtoupper(substr(str_replace([' ', ','], '', $subName), 0, 4))]
    );

    if (isset($cfg['nip'])) {
        $teacher = Teacher::where('nip', $cfg['nip'])->first();
    } else {
        $teacher = Teacher::where('name', 'like', "%{$cfg['name']}%")->first();
    }

    if (!$teacher) continue;

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

    $csMap[$subName] = ['subject' => $subject, 'teacher' => $teacher, 'cs' => $cs];
}

// Hapus seluruh jadwal lama
Schedule::where('class_id', $class->id)->delete();

$times = [
    1 => ['start' => '07:15', 'end' => '08:30'],
    2 => ['start' => '08:30', 'end' => '09:45'],
    3 => ['start' => '10:00', 'end' => '11:15'],
    4 => ['start' => '11:15', 'end' => '12:30'],
];

// JADWAL SENIN - JUMAT (5 HARI SAJA, SABTU & MINGGU KOSONG/LIBUR)
$dailySubjects = [
    1 => ['Bahasa Indonesia', 'Matematika Wajib', 'Fisika', 'Pendidikan Agama Islam'], // Senin (Ammar Syarif di Jam 1)
    2 => ['Bahasa Inggris', 'Biologi', 'Kimia', 'Pendidikan Pancasila dan Kewarganegaraan'], // Selasa (Ammar Syarif di Jam 1)
    3 => ['Matematika Wajib', 'Fisika', 'Biologi', 'Informatika'], // Rabu (TIDAK ADA Ammar Syarif)
    4 => ['Sejarah Indonesia', 'Kimia', 'Pendidikan Jasmani Olahraga, Kesehatan', 'Matematika Wajib'], // Kamis (Ammar Syarif di Jam 1)
    5 => ['Pendidikan Agama Islam', 'Fisika', 'Biologi', 'Pendidikan Pancasila dan Kewarganegaraan'], // Jumat (TIDAK ADA Ammar Syarif)
];

$dayNames = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

foreach ($dailySubjects as $dayOfWeek => $mapels) {
    echo "\n  [Hari {$dayNames[$dayOfWeek]}]:\n";
    foreach ($mapels as $slotIdx => $subName) {
        $slotNum = $slotIdx + 1;
        $tInfo = $csMap[$subName] ?? null;
        if (!$tInfo) continue;

        Schedule::create([
            'class_id' => $class->id,
            'teacher_id' => $tInfo['teacher']->id,
            'subject' => $subName,
            'academic_year_id' => $academicYear?->id,
            'day_of_week' => $dayOfWeek,
            'start_time' => $times[$slotNum]['start'],
            'end_time' => $times[$slotNum]['end'],
            'room' => 'Ruang XII IPA 1',
        ]);

        echo "    - Jam {$slotNum} ({$times[$slotNum]['start']}-{$times[$slotNum]['end']}): {$subName} (Guru: {$tInfo['teacher']->name})\n";
    }
}

echo "\nBERHASIL! Jadwal pelajaran sekarang MURNI SENIN-JUMAT saja (Sabtu & Minggu libur). Guru Ammar Syarif hanya mengajar di hari Senin, Selasa, dan Kamis.\n";

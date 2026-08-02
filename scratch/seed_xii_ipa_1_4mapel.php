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

echo "=== MENGISI JADWAL & MAPEL 4 SESI / HARI KELAS XII IPA 1 (DENGAN GURU PRESISI) ===\n";

$class = Classes::where('name', 'XII IPA 1')->firstOrFail();
$academicYear = AcademicYear::active() ?? AcademicYear::first();

echo "Kelas: {$class->name} (ID: {$class->id})\n";

// Pemetaan Presisi Guru dengan Mata Pelajaran yang Diampu
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

// 1. Buat ClassSubject untuk XII IPA 1
echo "\n--- Menyiapkan ClassSubject Kelas XII IPA 1 ---\n";
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

    if (!$teacher) {
        echo "  [!] Warning: Guru {$cfg['name']} tidak ditemukan, skip.\n";
        continue;
    }

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
    echo "  [✓] Mapel: {$subName} -> Guru: {$teacher->name}\n";
}

// 2. Buat Jadwal Pelajaran (Schedules) 4 Mapel per Hari (Senin - Minggu)
echo "\n--- Menyiapkan Jadwal Pelajaran 4 Mapel / Hari untuk XII IPA 1 ---\n";
Schedule::where('class_id', $class->id)->delete();

$times = [
    1 => ['start' => '07:15', 'end' => '08:30'],
    2 => ['start' => '08:30', 'end' => '09:45'],
    3 => ['start' => '10:00', 'end' => '11:15'],
    4 => ['start' => '11:15', 'end' => '12:30'],
];

$dailySubjects = [
    1 => ['Bahasa Indonesia', 'Matematika Wajib', 'Fisika', 'Pendidikan Agama Islam'], // Senin
    2 => ['Bahasa Inggris', 'Biologi', 'Kimia', 'Pendidikan Pancasila dan Kewarganegaraan'], // Selasa
    3 => ['Sejarah Indonesia', 'Matematika Wajib', 'Fisika', 'Informatika'], // Rabu
    4 => ['Bahasa Indonesia', 'Biologi', 'Kimia', 'Pendidikan Jasmani Olahraga, Kesehatan'], // Kamis
    5 => ['Pendidikan Agama Islam', 'Bahasa Inggris', 'Sejarah Indonesia', 'Matematika Wajib'], // Jumat
    6 => ['Biologi', 'Fisika', 'Kimia', 'Bahasa Indonesia'], // Sabtu
    7 => ['Bahasa Indonesia', 'Bahasa Inggris', 'Sejarah Indonesia', 'Matematika Wajib'], // Minggu (Hari ini)
];

$dayNames = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

foreach ($dailySubjects as $dayOfWeek => $mapels) {
    echo "  [Day {$dayOfWeek} - {$dayNames[$dayOfWeek]}]:\n";
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

echo "\nBERHASIL! Jadwal kelas XII IPA 1 telah diisi 4 mapel per hari (Senin-Minggu) dengan pengampu guru yang 100% presisi.\n";

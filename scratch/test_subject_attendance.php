<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\ClassSubject;
use App\Models\Attendance;
use App\Models\AttendanceDetail;

$user = User::with('student')->where('role', \App\Enums\UserRole::Siswa)->first();
$student = $user->student;

if (!$student) {
    echo "Student user not found.\n";
    exit;
}

$cs = ClassSubject::where('class_id', $student->class_id)->first();
if (!$cs) {
    echo "ClassSubject not found for student's class.\n";
    exit;
}

echo "=== SIMULASI ABSENSI MAPEL SISWA ===\n";
echo "Siswa: {$student->name} (NISN: {$student->nisn})\n";
echo "Kelas: {$cs->class->name} | Mapel: {$cs->subject->name}\n";

// Simulasi Klik Hadir Siswa
$att = Attendance::firstOrCreate([
    'date' => today(),
    'class_id' => $cs->class_id,
    'class_subject_id' => $cs->id,
], [
    'teacher_id' => $cs->teacher_id,
    'session' => 'pagi',
    'notes' => 'Absensi Mandiri Siswa - Mapel ' . $cs->subject->name,
]);

// Buat default status alfa untuk siswa lain
$allStudents = Student::where('class_id', $cs->class_id)->get();
foreach ($allStudents as $s) {
    AttendanceDetail::firstOrCreate([
        'attendance_id' => $att->id,
        'student_id' => $s->id,
    ], [
        'status' => 'alfa',
    ]);
}

// Set status siswa ini menjadi HADIR
$detail = AttendanceDetail::where('attendance_id', $att->id)
    ->where('student_id', $student->id)
    ->first();

$detail->update([
    'status' => 'hadir',
    'check_in' => now(),
]);

echo "Status Absen Siswa setelah Klik Hadir: " . strtoupper($detail->fresh()->status) . " (Jam: " . $detail->fresh()->check_in->format('H:i') . " WIB)\n";

// Simulasi Guru Override ke ALFA (Tidak Hadir)
$detail->update(['status' => 'alfa']);
echo "Status Absen Siswa setelah Guru Override ke Tidak Hadir: " . strtoupper($detail->fresh()->status) . "\n";

<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ClassSubject;
use App\Models\User;

$user = User::where('role', \App\Enums\UserRole::Siswa)->first();
auth()->login($user);

$cs = ClassSubject::with('subject')->first();

echo "=== TESTING WINDOW WAKTU PRESENSI SISWA ===\n";
echo "Siswa: {$user->name}\n";
echo "Mapel: {$cs->subject->name}\n";

$controller = app(\App\Http\Controllers\StudentAssignmentController::class);
$response = $controller->showClassSubject($cs);
$viewData = $response->getData();
$win = $viewData['attendanceWindow'];

echo "Status Window Presensi: " . strtoupper($win['status']) . "\n";
echo "Bisa Klik Hadir? " . ($win['can_check_in'] ? 'YA (AKTIF)' : 'TIDAK (TIDAK AKTIF / TERKUNCI)') . "\n";
echo "Pesan: " . $win['message'] . "\n";

<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::where('email', 'ammarsyrf78@gmail.com')->first();
if (!$user) {
    echo "User Ammar Syarif tidak ditemukan.\n";
    exit;
}

auth()->login($user);

$req = \Illuminate\Http\Request::create('/akademik/attendance/create', 'GET', [
    'class_id' => 6,
]);

$controller = app(\App\Http\Controllers\AttendanceController::class);
$response = $controller->create($req);

$viewData = $response->getData();
echo "=== HASIL DROPDOWN PRESENSI GURU AMMAR SYARIF ===\n";
echo "Jumlah Kelas Tampil: " . count($viewData['classes']) . "\n";
foreach ($viewData['classes'] as $c) {
    echo "  - Kelas: {$c->name}\n";
}

echo "Sesi Presensi Tampil di Dropdown:\n";
foreach ($viewData['sessionLabels'] as $k => $info) {
    if (is_array($info)) {
        echo "  - [{$k}] => " . ($info['label'] ?? '') . "\n";
    }
}

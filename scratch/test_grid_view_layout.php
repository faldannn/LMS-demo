<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$guru = User::where('email', 'ammarsyrf78@gmail.com')->first();
auth()->login($guru);

view()->share('errors', new \Illuminate\Support\ViewErrorBag);

$req = \Illuminate\Http\Request::create('/akademik/attendance/create', 'GET', [
    'class_id' => 6,
    'session' => 'sched_109',
]);

$controller = app(\App\Http\Controllers\AttendanceController::class);
$response = $controller->create($req);
$html = $response->render();

echo "=== TESTING RENDER VIEW DETAIL PRESENSI MAPEL GURU ===\n";
echo "Ukuran HTML Ter-render: " . strlen($html) . " bytes\n";
echo "Ada elemen 'student-att-card'? " . (str_contains($html, 'student-att-card') ? 'YA (BERHASIL)' : 'TIDAK') . "\n";
echo "Ada pencarian 'student-search-input'? " . (str_contains($html, 'student-search-input') ? 'YA (BERHASIL)' : 'TIDAK') . "\n";
echo "Ada tombol 'Mode Kartu (Grid)'? " . (str_contains($html, 'Mode Kartu (Grid)') ? 'YA (BERHASIL)' : 'TIDAK') . "\n";

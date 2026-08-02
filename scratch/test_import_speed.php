<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Imports\StudentsImport;

echo "=== BENCHMARK SPEED IMPORT SISWA ===\n";
$import = new StudentsImport();

$dummyRows = [];
for ($i = 1; $i <= 100; $i++) {
    $nisn = "2026999" . str_pad($i, 4, "0", STR_PAD_LEFT);
    $dummyRows[] = [
        'nisn' => $nisn,
        'nama_lengkap' => "Siswa Benchmark " . $i,
        'kelas' => "XII IPA 1",
        'tanggal_lahir' => "2008-05-15",
    ];
}

$startTime = microtime(true);

foreach ($dummyRows as $row) {
    $import->model($row);
}

$endTime = microtime(true);
$duration = round($endTime - $startTime, 3);

echo "Berhasil memproses 100 siswa dalam waktu: {$duration} detik!\n";
echo "Rata-rata per siswa: " . round(($duration / 100) * 1000, 2) . " ms\n";

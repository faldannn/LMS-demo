<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Classes;

echo "=== DAFTAR KELAS DI LMS (AUTO-CREATED DARI EXCEL) ===\n";
$classes = Classes::withCount('students')->orderBy('name')->get();

foreach ($classes as $c) {
    echo "- Nama Kelas: {$c->name} | Tingkat/Grade: {$c->grade} | Jumlah Siswa: {$c->students_count}\n";
}

echo "Total Kelas: " . $classes->count() . "\n";

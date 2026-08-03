<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;
use App\Models\User;

echo "=== INSPEKSI SISWA DENGAN NAMA BENCHMARK / TEST / DUMMY ===\n";

$students = Student::where('name', 'like', '%benchmark%')
    ->orWhere('name', 'like', '%test%')
    ->orWhere('name', 'like', '%dummy%')
    ->orWhere('name', 'like', '%budi%')
    ->get();

echo "Ditemukan " . $students->count() . " siswa:\n";

foreach ($students as $s) {
    echo "- ID: {$s->id} | Name: {$s->name} | NISN: {$s->nis} | Email: {$s->email} | User ID: {$s->user_id}\n";
}

<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Teacher;
use App\Models\Subject;

echo "=== INSPEKSI DATA GURU & MAPEL YANG DIAMPU ===\n";

$teachers = Teacher::orderBy('name')->get();
foreach ($teachers as $t) {
    echo "ID: {$t->id} | Guru: {$t->name} | NIP: {$t->nip} | Mapel: {$t->subject}\n";
}

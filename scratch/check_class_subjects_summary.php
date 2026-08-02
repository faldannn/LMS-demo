<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Classes;
use App\Models\ClassSubject;

echo "=== JUMLAH MATA PELAJARAN (CLASS SUBJECTS) PER KELAS ===\n";
foreach (Classes::orderBy('name')->get() as $c) {
    $count = ClassSubject::where('class_id', $c->id)->count();
    echo "- Kelas {$c->name} (ID {$c->id}): {$count} Mata Pelajaran E-Learning\n";
}
echo "Total ClassSubjects: " . ClassSubject::count() . "\n";

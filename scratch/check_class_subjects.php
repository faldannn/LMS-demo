<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\ClassSubject;

echo "=== MATA PELAJARAN (SUBJECTS) ===\n";
foreach (Subject::all() as $s) {
    echo "- ID {$s->id}: {$s->name} ({$s->code})\n";
}

echo "\n=== GURU (TEACHERS) ===\n";
foreach (Teacher::all() as $t) {
    echo "- ID {$t->id}: {$t->name}\n";
}

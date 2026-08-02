<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ClassSubject;
use App\Models\Teacher;
use App\Models\Classes;

echo "=== INSPEKSI DATA CLASS_SUBJECTS & GURU ===\n";
echo "Total ClassSubject: " . ClassSubject::count() . "\n";

$teachers = Teacher::withCount('classSubjects')->get();
foreach ($teachers as $t) {
    echo "Guru: {$t->name} (ID: {$t->id}) -> Memegang {$t->class_subjects_count} ClassSubject\n";
}

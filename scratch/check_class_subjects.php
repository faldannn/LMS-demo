<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Total ClassSubject: " . \App\Models\ClassSubject::count() . "\n";
echo "Orphaned (class null or deleted): " . \App\Models\ClassSubject::whereNull('class_id')->orWhereDoesntHave('class')->count() . "\n";

$orphans = \App\Models\ClassSubject::whereNull('class_id')->orWhereDoesntHave('class')->get();
foreach ($orphans as $o) {
    echo "ID: {$o->id}, class_id: " . var_export($o->class_id, true) . ", subject_id: {$o->subject_id}, teacher_id: {$o->teacher_id}\n";
}

<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "All Class Subjects in DB:\n";
foreach (App\Models\ClassSubject::with('class')->get() as $cs) {
    echo sprintf(
        "CS ID: %d | Class ID: %s | Class Name: %s | CS Academic Year ID: %s\n",
        $cs->id,
        $cs->class_id ?? 'NULL',
        $cs->class?->name ?? 'NULL',
        $cs->academic_year_id ?? 'NULL'
    );
}

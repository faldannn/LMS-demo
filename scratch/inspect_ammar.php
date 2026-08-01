<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$teacher = App\Models\Teacher::where('name', 'like', '%Ammar%')->first();
echo "Teacher ID: {$teacher->id} | Name: {$teacher->name}" . PHP_EOL;

echo "=== SCHEDULES TAUGHT BY AMMAR SYARIF IN XII IPA 1 ===" . PHP_EOL;
foreach (App\Models\Schedule::where('teacher_id', $teacher->id)->where('class_id', 6)->get() as $s) {
    echo "Day: {$s->day_of_week} | Subject: {$s->subject}" . PHP_EOL;
}

echo "=== CLASS SUBJECTS TAUGHT BY AMMAR SYARIF IN XII IPA 1 ===" . PHP_EOL;
foreach (App\Models\ClassSubject::where('teacher_id', $teacher->id)->where('class_id', 6)->with('subject')->get() as $cs) {
    echo "Subject: " . ($cs->subject?->name ?? 'N/A') . PHP_EOL;
}

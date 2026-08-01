<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$schedules = App\Models\Schedule::with('teacher')->where('class_id', 6)->orderBy('day_of_week')->orderBy('start_time')->get();

foreach ($schedules as $index => $s) {
    $time = $s->start_time && $s->end_time ? $s->start_time->format('H:i') . '-' . $s->end_time->format('H:i') : 'N/A';
    echo "Day {$s->day_of_week} ({$s->day_name}) | Time: {$time} | Subject: {$s->subject} | Teacher: " . ($s->teacher?->name ?? 'N/A') . " (ID: {$s->teacher_id})" . PHP_EOL;
}

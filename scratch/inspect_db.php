<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$class = App\Models\Classes::where('name', 'XII IPA 1')->first();
echo "=== SCHEDULES FOR XII IPA 1 ===" . PHP_EOL;
foreach (App\Models\Schedule::where('class_id', $class->id)->with('teacher')->get() as $s) {
    echo "Day {$s->day_of_week} | {$s->subject} | Teacher: " . ($s->teacher?->name ?? 'N/A') . PHP_EOL;
}

echo PHP_EOL . "=== SAMPLE ATTENDANCE RECORDS FOR JULY 2026 ===" . PHP_EOL;
foreach (App\Models\Attendance::where('class_id', $class->id)->where('date', '2026-07-27')->with('teacher')->get() as $att) {
    echo "Date: {$att->date->toDateString()} | Session: {$att->session} | Teacher: " . ($att->teacher?->name ?? 'N/A') . PHP_EOL;
}

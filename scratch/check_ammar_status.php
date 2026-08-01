<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AttendanceDetail;
use App\Models\Student;

$s = Student::where('name', 'like', '%Ammar%')->first();
if ($s) {
    echo "Student: " . $s->name . " (ID: " . $s->id . ")\n";
    $details = AttendanceDetail::with('attendance')->where('student_id', $s->id)->get();
    foreach ($details as $d) {
        if (in_array(strtolower($d->status), ['sakit', 'izin'])) {
            echo "Date: " . $d->attendance->date->toDateString() . " | Session: " . $d->attendance->session . " | Status: [" . $d->status . "] | Notes: " . $d->notes . "\n";
        }
    }
}

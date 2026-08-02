<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;
use App\Models\Classes;
use App\Models\Setting;

echo "Total Students in DB: " . Student::count() . "\n";
echo "Total Classes in DB: " . Classes::count() . "\n";
echo "Kartu API Key: " . Setting::get('kartu_api_key') . "\n";
echo "Kartu Last Synced At: " . Setting::get('kartu_last_synced_at', '-') . "\n";

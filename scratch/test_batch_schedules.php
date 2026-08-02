<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Classes;
use App\Models\Schedule;

$class = Classes::where('name', 'X.1')->first() ?: Classes::first();
echo "Kelas Uji Coba: {$class->name} (ID: {$class->id})\n";

$schedulesCount = Schedule::where('class_id', $class->id)->count();
echo "Jumlah Jadwal Eksisting untuk Kelas {$class->name}: {$schedulesCount} slot.\n";

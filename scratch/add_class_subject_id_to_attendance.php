<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasColumn('attendance', 'class_subject_id')) {
    Schema::table('attendance', function (Blueprint $table) {
        $table->foreignId('class_subject_id')->nullable()->after('class_id')->constrained('class_subject')->nullOnDelete();
    });
    echo "Column class_subject_id added to attendance table.\n";
} else {
    echo "Column class_subject_id already exists in attendance table.\n";
}

<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasColumn('users', 'password_changed_at')) {
    Schema::table('users', function (Blueprint $table) {
        $table->timestamp('password_changed_at')->nullable()->after('status');
    });
    echo "Column password_changed_at added to users table.\n";
} else {
    echo "Column password_changed_at already exists.\n";
}

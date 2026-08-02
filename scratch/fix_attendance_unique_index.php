<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "=== MEMPERBAIKI INDEX UNIK TABEL ATTENDANCE ===\n";

try {
    // Drop old unique constraint if present
    Schema::table('attendance', function (Blueprint $table) {
        $table->dropUnique('attendance_date_class_id_session_unique');
    });
    echo "Index unik lama (attendance_date_class_id_session_unique) berhasil dihapus.\n";
} catch (\Throwable $e) {
    echo "Info: " . $e->getMessage() . "\n";
}

try {
    // Add new unique index including class_subject_id
    Schema::table('attendance', function (Blueprint $table) {
        $table->unique(['date', 'class_id', 'class_subject_id', 'session'], 'att_date_class_subject_session_unique');
    });
    echo "Index unik baru (att_date_class_subject_session_unique) berhasil dibuat.\n";
} catch (\Throwable $e) {
    echo "Info index baru: " . $e->getMessage() . "\n";
}

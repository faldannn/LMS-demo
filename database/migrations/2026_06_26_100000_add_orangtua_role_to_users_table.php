<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'guru', 'siswa', 'kepala_sekolah', 'orangtua') NOT NULL DEFAULT 'guru'");
        } elseif ($driver === 'sqlite') {
            // SQLite does not support ALTER ENUM — recreate constraint via table rebuild is handled
            // by fresh migrations; for existing SQLite dev DBs, role is stored as string.
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'guru', 'siswa', 'kepala_sekolah') NOT NULL DEFAULT 'guru'");
        }
    }
};

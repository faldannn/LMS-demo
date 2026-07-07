<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('users')->where('role', 'admin')->update(['role' => 'kepala_sekolah']);
        DB::table('users')->where('role', 'orangtua')->update(['role' => 'siswa']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('kepala_sekolah', 'guru', 'siswa') NOT NULL DEFAULT 'guru'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'guru', 'siswa', 'kepala_sekolah', 'orangtua') NOT NULL DEFAULT 'guru'");
        }
    }
};

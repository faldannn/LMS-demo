<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
 
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First alter enum values to include superadmin and admin
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('kepala_sekolah', 'guru', 'siswa', 'admin', 'superadmin') NOT NULL DEFAULT 'guru'");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('guru')->change();
            });
        }

        // Update kepala_sekolah to admin
        DB::table('users')->where('role', 'kepala_sekolah')->update(['role' => 'admin']);

        // Clean up the enum to remove kepala_sekolah
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('superadmin', 'admin', 'guru', 'siswa') NOT NULL DEFAULT 'guru'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Allow kepala_sekolah back in enum
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('superadmin', 'admin', 'guru', 'siswa', 'kepala_sekolah') NOT NULL DEFAULT 'guru'");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('guru')->change();
            });
        }

        // Revert admin to kepala_sekolah
        DB::table('users')->where('role', 'admin')->update(['role' => 'kepala_sekolah']);

        // Revert to original enum list
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('kepala_sekolah', 'guru', 'siswa') NOT NULL DEFAULT 'guru'");
        }
    }
};


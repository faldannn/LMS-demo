<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE students MODIFY gender ENUM('L', 'P', 'T') NOT NULL DEFAULT 'T'");
    }

    public function down(): void
    {
        DB::statement("UPDATE students SET gender = 'T' WHERE gender NOT IN ('L', 'P')");
        DB::statement("ALTER TABLE students MODIFY gender ENUM('L', 'P') NOT NULL");
    }
};

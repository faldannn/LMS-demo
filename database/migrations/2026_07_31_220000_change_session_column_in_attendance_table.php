<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        try {
            DB::statement("ALTER TABLE attendance MODIFY COLUMN session VARCHAR(50) NOT NULL DEFAULT 'pagi'");
        } catch (\Exception $e) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->string('session', 50)->default('pagi')->change();
            });
        }
    }

    public function down(): void
    {
    }
};

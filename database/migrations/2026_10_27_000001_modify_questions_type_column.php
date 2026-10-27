<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            DB::statement("ALTER TABLE questions MODIFY COLUMN type VARCHAR(50) NOT NULL DEFAULT 'multiple_choice'");
        } catch (\Throwable $e) {
            Schema::table('questions', function (Blueprint $table) {
                $table->string('type', 50)->default('multiple_choice')->change();
            });
        }

        try {
            DB::statement("ALTER TABLE questions MODIFY COLUMN image_path MEDIUMTEXT NULL");
        } catch (\Throwable $e) {
            Schema::table('questions', function (Blueprint $table) {
                $table->mediumText('image_path')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE questions MODIFY COLUMN type ENUM('multiple_choice', 'essay') NOT NULL DEFAULT 'multiple_choice'");
        } catch (\Throwable $e) {
            // Ignore rollback errors
        }
    }
};

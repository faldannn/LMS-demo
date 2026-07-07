<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = ['materials', 'assignments', 'quizzes'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table_blueprint) use ($table) {
                if (!Schema::hasColumn($table, 'uuid')) {
                    $table_blueprint->uuid('uuid')->nullable()->after('id');
                }
                if (!Schema::hasColumn($table, 'deleted_at')) {
                    $table_blueprint->softDeletes();
                }
            });

            // Populate UUIDs for existing records
            if (Schema::hasColumn($table, 'uuid')) {
                $records = DB::table($table)->whereNull('uuid')->get(['id']);
                foreach ($records as $record) {
                    DB::table($table)
                        ->where('id', $record->id)
                        ->update(['uuid' => Str::uuid()->toString()]);
                }
                
                // Make UUID non-nullable and unique after populating
                Schema::table($table, function (Blueprint $table_blueprint) {
                    $table_blueprint->uuid('uuid')->nullable(false)->unique()->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['materials', 'assignments', 'quizzes'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table_blueprint) use ($table) {
                if (Schema::hasColumn($table, 'uuid')) {
                    $table_blueprint->dropColumn('uuid');
                }
                if (Schema::hasColumn($table, 'deleted_at')) {
                    $table_blueprint->dropSoftDeletes();
                }
            });
        }
    }
};

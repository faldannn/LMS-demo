<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'class_subject_id')) {
                $table->foreignId('class_subject_id')
                    ->nullable()
                    ->after('class_id')
                    ->constrained('class_subject')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            if (Schema::hasColumn('attendance', 'class_subject_id')) {
                $table->dropForeign(['class_subject_id']);
                $table->dropColumn('class_subject_id');
            }
        });
    }
};

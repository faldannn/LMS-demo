<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('question_bank_id')->nullable()->after('id')->constrained('question_banks')->cascadeOnDelete();
            $table->string('difficulty', 20)->default('medium')->after('type');
            $table->boolean('is_active')->default(true)->after('order_number');
            $table->foreignId('created_by')->nullable()->after('question_bank_id')->constrained('users')->nullOnDelete();
        });

        Schema::table('question_options', function (Blueprint $table) {
            $table->unsignedSmallInteger('order_number')->default(0)->after('is_correct');
        });

        $this->migrateExistingQuestionsToBanks();

        Schema::table('questions', function (Blueprint $table) {
            $table->index(['question_bank_id', 'type', 'is_active']);
            $table->index(['question_bank_id', 'difficulty']);
        });
    }

    private function migrateExistingQuestionsToBanks(): void
    {
        $quizIds = DB::table('questions')->whereNotNull('quiz_id')->distinct()->pluck('quiz_id');

        foreach ($quizIds as $quizId) {
            $quiz = DB::table('quizzes')->where('id', $quizId)->first();
            if (!$quiz) {
                continue;
            }

            $bankId = DB::table('question_banks')->insertGetId([
                'class_subject_id' => $quiz->class_subject_id,
                'title' => 'Bank Soal — ' . $quiz->title,
                'description' => 'Dimigrasi otomatis dari ujian #' . $quizId,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('questions')
                ->where('quiz_id', $quizId)
                ->update(['question_bank_id' => $bankId]);
        }
    }

    public function down(): void
    {
        Schema::table('question_options', function (Blueprint $table) {
            $table->dropColumn('order_number');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('question_bank_id');
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn(['difficulty', 'is_active']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('question_bank_id')->nullable()->after('class_subject_id')
                ->constrained('question_banks')->nullOnDelete();
            $table->string('selection_mode', 20)->default('fixed')->after('show_results')
                ->comment('fixed|random|all_bank');
            $table->unsignedSmallInteger('random_question_count')->nullable()->after('selection_mode');
            $table->boolean('shuffle_questions')->default(false)->after('random_question_count');
            $table->boolean('shuffle_answers')->default(false)->after('shuffle_questions');
            $table->boolean('auto_submit_on_timeout')->default(true)->after('shuffle_answers');
            $table->decimal('pass_score', 5, 2)->default(75)->after('auto_submit_on_timeout');
            $table->unsignedTinyInteger('questions_per_page')->default(1)->after('pass_score');
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('question_bank_id');
            $table->dropColumn([
                'selection_mode', 'random_question_count', 'shuffle_questions',
                'shuffle_answers', 'auto_submit_on_timeout', 'pass_score', 'questions_per_page',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE quiz_attempts MODIFY status VARCHAR(20) NOT NULL DEFAULT 'ongoing'");
        }

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dateTime('expires_at')->nullable()->after('start_time');
            $table->decimal('total_points', 8, 2)->nullable()->after('score');
            $table->decimal('earned_points', 8, 2)->nullable()->after('total_points');
            $table->boolean('needs_grading')->default(false)->after('earned_points');
        });

        Schema::table('student_answers', function (Blueprint $table) {
            $table->boolean('is_correct')->nullable()->after('essay_answer');
            $table->text('feedback')->nullable()->after('score');
            $table->foreignId('graded_by')->nullable()->after('feedback')->constrained('users')->nullOnDelete();
            $table->timestamp('graded_at')->nullable()->after('graded_by');
        });

        Schema::table('student_answers', function (Blueprint $table) {
            $table->index(['quiz_attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::table('student_answers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('graded_by');
            $table->dropColumn(['is_correct', 'feedback', 'graded_at']);
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropColumn(['expires_at', 'total_points', 'earned_points', 'needs_grading']);
        });
    }
};

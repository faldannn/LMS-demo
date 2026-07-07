<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cbt_cheat_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')->constrained('quiz_attempts')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->string('violation_type', 50); // tab_switch, copy, paste, right_click, fullscreen_exit, devtools
            $table->unsignedInteger('count')->default(1);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable(); // extra context like page number, question id
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['quiz_attempt_id', 'violation_type']);
            $table->index(['student_id', 'quiz_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cbt_cheat_logs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quiz_attempt_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')->constrained('quiz_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->json('option_order')->nullable()->comment('Urutan opsi untuk attempt ini');
            $table->timestamps();

            $table->unique(['quiz_attempt_id', 'question_id']);
            $table->index(['quiz_attempt_id', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempt_questions');
    }
};

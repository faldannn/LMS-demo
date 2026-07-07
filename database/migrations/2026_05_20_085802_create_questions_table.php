<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->nullable()->constrained('quizzes')->nullOnDelete();
            $table->text('question_text');
            $table->string('image_path')->nullable();
            $table->enum('type', ['multiple_choice', 'essay'])->default('multiple_choice');
            $table->unsignedSmallInteger('points')->default(1);
            $table->unsignedSmallInteger('order_number')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};

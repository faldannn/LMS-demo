<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('quiz_classes')) {
            Schema::create('quiz_classes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
                $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['quiz_id', 'class_id']);
                $table->index('quiz_id');
                $table->index('class_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_classes');
    }
};

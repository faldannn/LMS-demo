<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->enum('session', ['pagi', 'siang'])->default('pagi');
            $table->text('notes')->nullable();
            $table->timestamps();

            // One session per class per day
            $table->unique(['date', 'class_id', 'session']);
            $table->index(['date', 'class_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
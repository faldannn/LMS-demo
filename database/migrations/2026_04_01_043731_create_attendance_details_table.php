<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendance')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alfa', 'terlambat'])->default('alfa');
            $table->time('check_in')->nullable();
            $table->text('notes')->nullable();

            $table->unique(['attendance_id', 'student_id']);
            $table->index(['student_id', 'status']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('attendance_details');
    }
};

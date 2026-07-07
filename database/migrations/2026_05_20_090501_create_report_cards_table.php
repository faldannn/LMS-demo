<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->enum('semester', ['ganjil', 'genap']);
            $table->integer('attendance_hadir')->default(0);
            $table->integer('attendance_izin')->default(0);
            $table->integer('attendance_sakit')->default(0);
            $table->integer('attendance_alfa')->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            
            $table->unique(['student_id', 'academic_year_id', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_cards');
    }
};

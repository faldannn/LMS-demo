<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_face_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('students')->cascadeOnDelete();
            $table->json('landmark_descriptor')->nullable();
            $table->string('reference_photo_path', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('self_attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('attendance_detail_id')->nullable()->constrained('attendance_details')->nullOnDelete();
            $table->date('attendance_date');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('distance_meters', 10, 2)->nullable();
            $table->boolean('within_radius')->default(false);
            $table->decimal('face_detection_confidence', 5, 4)->nullable();
            $table->decimal('face_match_confidence', 5, 4)->nullable();
            $table->boolean('face_matched')->default(false);
            $table->string('photo_path', 500)->nullable();
            $table->string('device_type', 50)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('check_in_mode', 30)->default('full');
            $table->string('status', 30)->default('success');
            $table->text('fallback_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('checked_in_at');
            $table->timestamps();

            $table->index(['student_id', 'attendance_date']);
            $table->index(['attendance_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('self_attendance_logs');
        Schema::dropIfExists('student_face_profiles');
    }
};

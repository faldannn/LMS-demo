<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('teacher_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('date');
            $table->string('status', 30)->default('hadir'); // hadir, terlambat, izin, sakit, alfa

            // Check-in (Masuk)
            $table->timestamp('check_in_at')->nullable();
            $table->string('check_in_photo', 500)->nullable();
            $table->decimal('check_in_lat', 10, 7)->nullable();
            $table->decimal('check_in_lng', 10, 7)->nullable();
            $table->decimal('check_in_distance_meters', 10, 2)->nullable();
            $table->boolean('check_in_within_radius')->default(false);
            $table->string('check_in_mode', 30)->default('full');

            // Check-out (Pulang)
            $table->timestamp('check_out_at')->nullable();
            $table->string('check_out_photo', 500)->nullable();
            $table->decimal('check_out_lat', 10, 7)->nullable();
            $table->decimal('check_out_lng', 10, 7)->nullable();
            $table->decimal('check_out_distance_meters', 10, 2)->nullable();
            $table->boolean('check_out_within_radius')->default(false);
            $table->string('check_out_mode', 30)->default('full');

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['teacher_id', 'date']);
            $table->index(['date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_attendances');
    }
};

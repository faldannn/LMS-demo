<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 20)->unique();
            $table->string('nisn', 20)->unique()->nullable();
            $table->string('name', 100);
            $table->enum('gender', ['L', 'P']);
            $table->date('dob')->nullable();
            $table->string('pob', 100)->nullable();  // place of birth
            $table->text('address')->nullable();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->string('parent_name', 100)->nullable();
            $table->string('parent_phone', 20)->nullable();
            $table->string('parent_email', 100)->nullable();
            $table->string('photo', 255)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
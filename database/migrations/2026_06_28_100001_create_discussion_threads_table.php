<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('discussion_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_subject_id')->constrained('class_subject')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('body');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->unsignedInteger('replies_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['class_subject_id', 'is_pinned']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discussion_threads');
    }
};

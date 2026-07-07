<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->unsignedSmallInteger('order_number')->default(0);
            $table->unsignedSmallInteger('points_override')->nullable();
            $table->timestamps();

            $table->unique(['quiz_id', 'question_id']);
            $table->index(['quiz_id', 'order_number']);
        });

        $pairs = DB::table('questions')
            ->whereNotNull('quiz_id')
            ->select('quiz_id', 'id as question_id', 'order_number')
            ->get();

        foreach ($pairs as $pair) {
            DB::table('quiz_questions')->insertOrIgnore([
                'quiz_id' => $pair->quiz_id,
                'question_id' => $pair->question_id,
                'order_number' => $pair->order_number,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};

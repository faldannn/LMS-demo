<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('question_options', function (Blueprint $table) {
            $table->text('match_text')->nullable()->after('option_text');
        });

        Schema::table('student_answers', function (Blueprint $table) {
            $table->json('matching_answer')->nullable()->after('essay_answer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_options', function (Blueprint $table) {
            $table->dropColumn('match_text');
        });

        Schema::table('student_answers', function (Blueprint $table) {
            $table->dropColumn('matching_answer');
        });
    }
};

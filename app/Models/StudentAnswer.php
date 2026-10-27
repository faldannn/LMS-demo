<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAnswer extends Model
{
    use HasFactory;

    protected $table = 'student_answers';

    protected $fillable = [
        'quiz_attempt_id',
        'question_id',
        'question_option_id',
        'essay_answer',
        'matching_answer',
        'is_correct',
        'score',
        'feedback',
        'graded_by',
        'graded_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'score' => 'float',
        'matching_answer' => 'array',
        'graded_at' => 'datetime',
    ];

    public function quizAttempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'question_option_id');
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}

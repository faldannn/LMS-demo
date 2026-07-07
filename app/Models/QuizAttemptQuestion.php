<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttemptQuestion extends Model
{
    protected $fillable = [
        'quiz_attempt_id',
        'question_id',
        'display_order',
        'option_order',
    ];

    protected $casts = [
        'option_order' => 'array',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}

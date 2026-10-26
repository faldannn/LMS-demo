<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';

    protected $fillable = [
        'quiz_id',
        'question_bank_id',
        'created_by',
        'question_text',
        'image_path',
        'type',
        'difficulty',
        'points',
        'order_number',
        'is_active',
    ];

    protected $casts = [
        'points' => 'integer',
        'order_number' => 'integer',
        'is_active' => 'boolean',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function questionBank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class, 'question_id')->orderBy('order_number');
    }

    public function studentAnswers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class, 'question_id');
    }

    public function getCorrectOptionAttribute(): ?QuestionOption
    {
        return $this->options->firstWhere('is_correct', true);
    }
}

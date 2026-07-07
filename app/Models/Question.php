<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    public const TYPES = ['multiple_choice', 'essay', 'matching'];

    public const DIFFICULTIES = ['easy', 'medium', 'hard'];

    protected $fillable = [
        'question_bank_id',
        'quiz_id',
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
        'is_active' => 'boolean',
    ];

    public function bank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id');
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order_number');
    }

    public function quizzes(): BelongsToMany
    {
        return $this->belongsToMany(Quiz::class, 'quiz_questions')
            ->withPivot(['order_number', 'points_override'])
            ->withTimestamps();
    }

    public function effectivePoints(?Quiz $quiz = null): int
    {
        if ($quiz) {
            $pivot = $this->quizzes()->where('quiz_id', $quiz->id)->first()?->pivot;

            return (int) ($pivot?->points_override ?? $this->points);
        }

        return (int) $this->points;
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'multiple_choice' => 'Pilihan Ganda',
            'essay' => 'Essay',
            'matching' => 'Menjodohkan',
            default => ucfirst($this->type),
        };
    }

    public function getDifficultyLabelAttribute(): string
    {
        return match ($this->difficulty) {
            'easy' => 'Mudah',
            'medium' => 'Sedang',
            'hard' => 'Sulit',
            default => ucfirst($this->difficulty),
        };
    }
}

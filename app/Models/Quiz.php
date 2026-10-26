<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Quiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'quizzes';

    protected $fillable = [
        'uuid',
        'class_subject_id',
        'question_bank_id',
        'title',
        'description',
        'duration',
        'start_time',
        'end_time',
        'is_published',
        'show_results',
        'selection_mode',
        'random_question_count',
        'shuffle_questions',
        'shuffle_answers',
        'auto_submit_on_timeout',
        'pass_score',
        'questions_per_page',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_published' => 'boolean',
        'show_results' => 'boolean',
        'shuffle_questions' => 'boolean',
        'shuffle_answers' => 'boolean',
        'auto_submit_on_timeout' => 'boolean',
        'duration' => 'integer',
        'pass_score' => 'float',
    ];

    public function classSubject(): BelongsTo
    {
        return $this->belongsTo(ClassSubject::class, 'class_subject_id');
    }

    public function questionBank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'quiz_id')->orderBy('order_number');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id');
    }

    public function isActive(): bool
    {
        if (!$this->is_published) {
            return false;
        }

        $now = now();
        if ($this->start_time && $now->lt($this->start_time)) {
            return false;
        }

        if ($this->end_time && $now->gt($this->end_time)) {
            return false;
        }

        return true;
    }

    public function getTotalPointsAttribute(): float
    {
        return (float) $this->questions()->sum('points');
    }

    public function getQuestionCountAttribute(): int
    {
        return $this->questions()->count();
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}

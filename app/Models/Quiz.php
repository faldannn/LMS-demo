<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Quiz extends Model
{
    use HasFactory, SoftDeletes;

    public const SELECTION_MODES = ['fixed', 'random', 'all_bank'];

    protected $fillable = [
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
        'pass_score' => 'decimal:2',
    ];

    public function classSubject(): BelongsTo
    {
        return $this->belongsTo(ClassSubject::class);
    }

    public function questionBank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class);
    }

    /** @deprecated Use bankQuestions() for CBT */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order_number');
    }

    public function bankQuestions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'quiz_questions')
            ->withPivot(['order_number', 'points_override'])
            ->orderByPivot('order_number');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function isOpen(): bool
    {
        $now = now();
        $isStarted = !$this->start_time || $now->isAfter($this->start_time);
        $isEnded = $this->end_time && $now->isAfter($this->end_time);

        return $this->is_published && $isStarted && !$isEnded;
    }

    public function totalConfiguredQuestions(): int
    {
        if ($this->selection_mode === 'random' && $this->random_question_count) {
            return (int) $this->random_question_count;
        }

        if ($this->selection_mode === 'all_bank' && $this->question_bank_id) {
            return $this->questionBank?->activeQuestions()->count() ?? 0;
        }

        return $this->bankQuestions()->count();
    }

    public function totalConfiguredPoints(): int
    {
        return (int) $this->bankQuestions()->get()->sum(fn (Question $q) => $q->effectivePoints($this));
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}

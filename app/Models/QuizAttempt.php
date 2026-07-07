<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'student_id',
        'start_time',
        'end_time',
        'expires_at',
        'score',
        'total_points',
        'earned_points',
        'needs_grading',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'expires_at' => 'datetime',
        'score' => 'decimal:2',
        'total_points' => 'decimal:2',
        'earned_points' => 'decimal:2',
        'needs_grading' => 'boolean',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class);
    }

    public function attemptQuestions(): HasMany
    {
        return $this->hasMany(QuizAttemptQuestion::class)->orderBy('display_order');
    }

    public function cheatLogs(): HasMany
    {
        return $this->hasMany(CbtCheatLog::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && now()->isAfter($this->expires_at);
    }

    public function isFinished(): bool
    {
        return in_array($this->status, ['finished', 'auto_submitted'], true);
    }

    public function remainingSeconds(): int
    {
        if (!$this->expires_at) {
            return 0;
        }

        return max(0, (int) now()->diffInSeconds($this->expires_at, false));
    }
}

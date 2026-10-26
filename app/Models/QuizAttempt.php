<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $table = 'quiz_attempts';

    protected $fillable = [
        'quiz_id',
        'student_id',
        'start_time',
        'expires_at',
        'end_time',
        'score',
        'total_points',
        'earned_points',
        'needs_grading',
        'status', // ongoing, completed, timed_out
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'expires_at' => 'datetime',
        'end_time' => 'datetime',
        'score' => 'float',
        'total_points' => 'float',
        'earned_points' => 'float',
        'needs_grading' => 'boolean',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class, 'quiz_attempt_id');
    }

    public function isOngoing(): bool
    {
        return $this->status === 'ongoing' && (!$this->expires_at || now()->lt($this->expires_at));
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, ['completed', 'timed_out']);
    }

    public function getRemainingSecondsAttribute(): int
    {
        if (!$this->isOngoing()) {
            return 0;
        }

        if (!$this->expires_at) {
            return $this->quiz->duration * 60;
        }

        return max(0, now()->diffInSeconds($this->expires_at, false));
    }
}

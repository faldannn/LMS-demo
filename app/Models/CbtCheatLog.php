<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CbtCheatLog extends Model
{
    protected $fillable = [
        'quiz_attempt_id',
        'student_id',
        'quiz_id',
        'violation_type',
        'count',
        'ip_address',
        'user_agent',
        'metadata',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
        'count' => 'integer',
    ];

    /* ── Violation Types ── */
    public const TYPE_TAB_SWITCH = 'tab_switch';
    public const TYPE_COPY = 'copy';
    public const TYPE_PASTE = 'paste';
    public const TYPE_RIGHT_CLICK = 'right_click';
    public const TYPE_FULLSCREEN_EXIT = 'fullscreen_exit';
    public const TYPE_DEVTOOLS = 'devtools';

    public const VALID_TYPES = [
        self::TYPE_TAB_SWITCH,
        self::TYPE_COPY,
        self::TYPE_PASTE,
        self::TYPE_RIGHT_CLICK,
        self::TYPE_FULLSCREEN_EXIT,
        self::TYPE_DEVTOOLS,
    ];

    /* ── Relationships ── */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /* ── Helpers ── */
    public function getViolationLabelAttribute(): string
    {
        return match ($this->violation_type) {
            self::TYPE_TAB_SWITCH => 'Pindah Tab/Aplikasi',
            self::TYPE_COPY => 'Menyalin Teks',
            self::TYPE_PASTE => 'Menempel Teks',
            self::TYPE_RIGHT_CLICK => 'Klik Kanan',
            self::TYPE_FULLSCREEN_EXIT => 'Keluar Fullscreen',
            self::TYPE_DEVTOOLS => 'Membuka DevTools',
            default => ucfirst(str_replace('_', ' ', $this->violation_type)),
        };
    }
}

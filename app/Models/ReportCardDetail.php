<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportCardDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_card_id',
        'subject_id',
        'assignment_score',
        'quiz_score',
        'final_score',
        'grade',
        'teacher_notes',
    ];

    protected $casts = [
        'assignment_score' => 'decimal:2',
        'quiz_score' => 'decimal:2',
        'final_score' => 'decimal:2',
    ];

    public function reportCard(): BelongsTo
    {
        return $this->belongsTo(ReportCard::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}

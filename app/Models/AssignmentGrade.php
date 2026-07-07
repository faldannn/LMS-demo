<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_submission_id',
        'teacher_id',
        'score',
        'feedback',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AssignmentSubmission::class, 'assignment_submission_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}

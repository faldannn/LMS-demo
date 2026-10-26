<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionBank extends Model
{
    use HasFactory;

    protected $table = 'question_banks';

    protected $fillable = [
        'class_subject_id',
        'title',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function classSubject(): BelongsTo
    {
        return $this->belongsTo(ClassSubject::class, 'class_subject_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'question_bank_id');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'question_bank_id');
    }
}

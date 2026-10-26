<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOption extends Model
{
    use HasFactory;

    protected $table = 'question_options';

    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
        'order_number',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'order_number' => 'integer',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}

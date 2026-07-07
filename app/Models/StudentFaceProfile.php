<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentFaceProfile extends Model
{
    protected $fillable = [
        'student_id',
        'landmark_descriptor',
        'reference_photo_path',
    ];

    protected $casts = [
        'landmark_descriptor' => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

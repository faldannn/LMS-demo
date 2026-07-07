<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'semester',
        'attendance_hadir',
        'attendance_izin',
        'attendance_sakit',
        'attendance_alfa',
        'notes',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(ReportCardDetail::class);
    }
}

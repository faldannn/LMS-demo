<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classes extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'grade',
        'major_id',
        'homeroom_teacher_id',
        'academic_year_id',
        'session',
        'room',
        'max_students',
    ];

    // ── Relations ──────────────────────────────────────────
    public function homeroomTeacher(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'homeroom_teacher_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function students(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function schedules(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }

    public function attendances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }

    public function classSubjects(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ClassSubject::class, 'class_id');
    }

    // ── Helpers ─────────────────────────────────────────────
    public function getStudentCountAttribute(): int
    {
        return $this->students()->where('status', true)->count();
    }

    public function getGradeLabelAttribute(): string
    {
        return match ($this->grade) {
            10 => 'X', 11 => 'XI', 12 => 'XII', default => (string) $this->grade,
        };
    }
}

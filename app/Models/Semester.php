<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Semester extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'academic_year_id',
        'number',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function getLabelAttribute(): string
    {
        return $this->number === 1 ? 'Ganjil' : 'Genap';
    }

    public function getFullLabelAttribute(): string
    {
        return ($this->academicYear?->year ?? '-') . ' — Semester ' . $this->label;
    }

    public static function active(): ?self
    {
        return static::where('is_active', true)->first();
    }

    public function scopeForYear($query, int $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }
}

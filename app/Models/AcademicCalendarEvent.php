<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicCalendarEvent extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'academic_year_id',
        'class_id',
        'created_by',
        'is_public',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_public' => 'boolean',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'akademik' => 'Akademik',
            'ujian' => 'Ujian',
            'libur' => 'Libur',
            'kegiatan' => 'Kegiatan',
            default => 'Lainnya',
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'akademik' => '#15803d',
            'ujian' => '#dc2626',
            'libur' => '#2563eb',
            'kegiatan' => '#7c3aed',
            default => '#64748b',
        };
    }
}

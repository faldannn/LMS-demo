<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'year',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function classes(): HasMany
    {
        return $this->hasMany(Classes::class);
    }

    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class);
    }

    public function reportCards(): HasMany
    {
        return $this->hasMany(ReportCard::class);
    }

    protected static function booted(): void
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('academic_year_active');
        });
        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('academic_year_active');
        });
    }

    public static function active(): ?self
    {
        try {
            return \Illuminate\Support\Facades\Cache::remember('academic_year_active', 86400, function () {
                return static::where('is_active', true)->with('semesters')->first();
            });
        } catch (\Throwable $e) {
            return static::where('is_active', true)->first();
        }
    }

    public function getFullLabelAttribute(): string
    {
        $activeSemester = $this->relationLoaded('semesters')
            ? $this->semesters->firstWhere('is_active', true)
            : $this->semesters()->where('is_active', true)->first();

        return $activeSemester
            ? "{$this->year} — Semester {$activeSemester->label}"
            : $this->year;
    }
}

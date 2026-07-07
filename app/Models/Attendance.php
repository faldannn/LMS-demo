<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'date',
        'class_id',
        'teacher_id',
        'session',
        'notes',
    ];

    protected $casts = ['date' => 'date'];

    // ── Relations ──────────────────────────────────────────
    public function class(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function teacher(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function details(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AttendanceDetail::class);
    }

    // ── Scopes ──────────────────────────────────────────────
    public function scopeForClass($query, int $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)
            ->whereYear('date', now()->year);
    }

    public function scopeDateRange($query, string $from, string $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    // ── Helpers ─────────────────────────────────────────────
    public function getSummaryAttribute(): array
    {
        $details = $this->details;
        return [
            'hadir' => $details->where('status', 'hadir')->count(),
            'sakit' => $details->where('status', 'sakit')->count(),
            'izin' => $details->where('status', 'izin')->count(),
            'alfa' => $details->where('status', 'alfa')->count(),
            'terlambat' => $details->where('status', 'terlambat')->count(),
            'total' => $details->count(),
        ];
    }
}
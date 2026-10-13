<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceDetail extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'attendance_id',
        'student_id',
        'status',
        'check_in',
        'notes',
    ];

    protected $casts = ['check_in' => 'datetime:H:i'];

    // ── Relations ──────────────────────────────────────────
    public function attendance(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    // ── Helpers ─────────────────────────────────────────────
    public function getFormattedCheckInAttribute(): string
    {
        if (!$this->check_in) {
            return '-';
        }

        if ($this->check_in instanceof \DateTimeInterface) {
            return $this->check_in->format('H:i');
        }

        try {
            return \Carbon\Carbon::parse($this->check_in)->format('H:i');
        } catch (\Throwable $e) {
            return (string) $this->check_in;
        }
    }

    public function getStatusLabelAttribute(): string

    {
        return match ($this->status) {
            'hadir' => 'Hadir',
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            'alfa' => 'Tidak Hadir (Alfa)',
            'terlambat' => 'Terlambat',
            default => '-',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'hadir' => 'success',
            'sakit' => 'warning',
            'izin' => 'info',
            'alfa' => 'danger',
            'terlambat' => 'secondary',
            default => 'light',
        };
    }
}
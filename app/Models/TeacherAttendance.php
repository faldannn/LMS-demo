<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'user_id',
        'date',
        'status',
        'check_in_at',
        'check_in_photo',
        'check_in_lat',
        'check_in_lng',
        'check_in_distance_meters',
        'check_in_within_radius',
        'check_in_mode',
        'check_out_at',
        'check_out_photo',
        'check_out_lat',
        'check_out_lng',
        'check_out_distance_meters',
        'check_out_within_radius',
        'check_out_mode',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'check_in_lat' => 'decimal:7',
        'check_in_lng' => 'decimal:7',
        'check_in_distance_meters' => 'decimal:2',
        'check_in_within_radius' => 'boolean',
        'check_out_lat' => 'decimal:7',
        'check_out_lng' => 'decimal:7',
        'check_out_distance_meters' => 'decimal:2',
        'check_out_within_radius' => 'boolean',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'hadir' => 'Hadir',
            'terlambat' => 'Terlambat',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alfa' => 'Alfa (Tidak Hadir)',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'hadir' => 'emerald',
            'terlambat' => 'amber',
            'izin' => 'sky',
            'sakit' => 'indigo',
            'alfa' => 'rose',
            default => 'slate',
        };
    }
}

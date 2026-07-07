<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SelfAttendanceLog extends Model
{
    protected $fillable = [
        'student_id',
        'user_id',
        'attendance_detail_id',
        'attendance_date',
        'latitude',
        'longitude',
        'distance_meters',
        'within_radius',
        'face_detection_confidence',
        'face_match_confidence',
        'face_matched',
        'photo_path',
        'device_type',
        'browser',
        'ip_address',
        'user_agent',
        'check_in_mode',
        'status',
        'fallback_reason',
        'notes',
        'checked_in_at',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'distance_meters' => 'decimal:2',
        'within_radius' => 'boolean',
        'face_detection_confidence' => 'decimal:4',
        'face_match_confidence' => 'decimal:4',
        'face_matched' => 'boolean',
        'checked_in_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceDetail(): BelongsTo
    {
        return $this->belongsTo(AttendanceDetail::class);
    }

    public function getModeLabelAttribute(): string
    {
        return match ($this->check_in_mode) {
            'full' => 'Lengkap (Wajah + GPS)',
            'gps_fallback' => 'Fallback GPS',
            'camera_fallback' => 'Fallback Kamera',
            'manual_fallback' => 'Fallback Manual',
            default => $this->check_in_mode,
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nis',
        'nisn',
        'rfid',
        'name',
        'gender',
        'dob',
        'pob',
        'address',
        'class_id',
        'parent_name',
        'parent_phone',
        'parent_email',
        'photo',
        'user_id',
        'status',
    ];

    protected $casts = [
        'dob' => 'date',
        'status' => 'boolean',
    ];

    // ── Relations ──────────────────────────────────────────
    public function class(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AttendanceDetail::class);
    }

    public function assignmentSubmissions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function reportCards(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ReportCard::class);
    }

    public function faceProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StudentFaceProfile::class);
    }

    public function selfAttendanceLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SelfAttendanceLog::class);
    }

    // ── Scopes ──────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    // ── Helpers ─────────────────────────────────────────────
    public function getGenderLabelAttribute(): string
    {
        return match ($this->gender) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => 'Belum diisi',
        };
    }

    public function getAgeAttribute(): int
    {
        return $this->dob ? $this->dob->age : 0;
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=8b5cf6&color=fff';
    }

    /**
     * Calculate attendance percentage.
     * When used with withCount, avoids N+1 queries.
     */
    public function getAttendancePercentageAttribute(): float
    {
        // If counts were preloaded via withCount, use them
        if (isset($this->attributes['attendance_details_count'])) {
            $total = (int) $this->attributes['attendance_details_count'];
            $present = (int) ($this->attributes['present_count'] ?? 0);
            return $total > 0 ? round($present / $total * 100, 1) : 0;
        }

        // Fallback: single query with conditional count
        $result = $this->attendanceDetails()
            ->selectRaw("COUNT(*) as total, SUM(status IN ('hadir','terlambat')) as present")
            ->first();

        $total = (int) ($result->total ?? 0);
        $present = (int) ($result->present ?? 0);
        return $total > 0 ? round($present / $total * 100, 1) : 0;
    }
}

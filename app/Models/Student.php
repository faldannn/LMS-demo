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
        'registration_type',
        'entry_date',
        'origin_school',
        'student_status',
        'exit_date',
        'exit_reason',
        'destination_school',
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
        'entry_date' => 'date',
        'exit_date' => 'date',
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

    public function academicHistories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentAcademicHistory::class)->orderByDesc('academic_year_id');
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

    public function getStudentStatusLabelAttribute(): string
    {
        return match ($this->student_status ?? ($this->status ? 'aktif' : 'non_aktif')) {
            'aktif' => 'Aktif',
            'lulus' => 'Lulus (Alumni)',
            'mutasi_keluar' => 'Pindah Sekolah',
            'drop_out' => 'Keluar / DO',
            default => 'Non-Aktif',
        };
    }

    public function getStudentStatusBadgeAttribute(): string
    {
        return match ($this->student_status ?? ($this->status ? 'aktif' : 'non_aktif')) {
            'aktif' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'lulus' => 'bg-purple-100 text-purple-800 border-purple-200',
            'mutasi_keluar' => 'bg-rose-100 text-rose-800 border-rose-200',
            'drop_out' => 'bg-amber-100 text-amber-800 border-amber-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    public function isPindahan(): bool
    {
        return ($this->registration_type ?? 'baru') === 'pindahan';
    }

    public function isMutasiKeluar(): bool
    {
        return in_array($this->student_status, ['mutasi_keluar', 'drop_out'], true);
    }
}

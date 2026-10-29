<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAcademicHistory extends Model
{
    use HasFactory;

    protected $table = 'student_academic_histories';

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'class_id',
        'status',
        'notes',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'aktif' => 'Aktif',
            'naik_kelas' => 'Naik Kelas',
            'tinggal_kelas' => 'Tinggal Kelas',
            'lulus' => 'Lulus',
            'mutasi_keluar' => 'Pindah Sekolah',
            'drop_out' => 'Drop Out / Keluar',
            default => ucfirst(str_replace('_', ' ', $this->status ?? 'Aktif')),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'aktif' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'naik_kelas' => 'bg-blue-100 text-blue-800 border-blue-200',
            'tinggal_kelas' => 'bg-amber-100 text-amber-800 border-amber-200',
            'lulus' => 'bg-purple-100 text-purple-800 border-purple-200',
            'mutasi_keluar' => 'bg-rose-100 text-rose-800 border-rose-200',
            'drop_out' => 'bg-slate-100 text-slate-800 border-slate-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }
}

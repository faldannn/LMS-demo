<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'name',
        'email',
        'role',
        'avatar',
        'last_login',
        'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'status' => 'boolean',
        'last_login' => 'datetime',
        'role' => UserRole::class,
    ];

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function studentParent(): HasOne
    {
        return $this->hasOne(StudentParent::class);
    }

    public function loginLogs(): HasMany
    {
        return $this->hasMany(LoginLog::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isKepalaSekolah(): bool
    {
        return $this->isAdmin() || $this->isSuperAdmin();
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function isGuru(): bool
    {
        return $this->role === UserRole::Guru;
    }

    public function isSiswa(): bool
    {
        return $this->role === UserRole::Siswa;
    }

    /** Kepala Sekolah atau Guru */
    public function isStaff(): bool
    {
        return $this->isKepalaSekolah() || $this->isGuru();
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        $current = $this->role instanceof UserRole ? $this->role->value : (string) $this->role;

        if (in_array('admin', $roles, true) || in_array('kepala_sekolah', $roles, true)) {
            if ($current === 'admin' || $current === 'superadmin') {
                return true;
            }
        }

        return in_array($current, $roles, true);
    }

    public function getRoleLabel(): string
    {
        if ($this->role instanceof UserRole) {
            return $this->role->label();
        }

        return UserRole::tryFrom((string) $this->role)?->label() ?? 'Unknown';
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=15803d&color=fff';
    }
}

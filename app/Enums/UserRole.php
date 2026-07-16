<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'superadmin';
    case Admin = 'admin';
    case Guru = 'guru';
    case Siswa = 'siswa';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Guru => 'Guru',
            self::Siswa => 'Siswa',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fromLegacy(string $role): self
    {
        return match ($role) {
            'superadmin' => self::SuperAdmin,
            'admin', 'kepala_sekolah' => self::Admin,
            'guru' => self::Guru,
            'siswa', 'orangtua' => self::Siswa,
            default => self::Guru,
        };
    }
}

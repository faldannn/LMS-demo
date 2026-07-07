<?php

namespace App\Enums;

enum UserRole: string
{
    case KepalaSekolah = 'kepala_sekolah';
    case Guru = 'guru';
    case Siswa = 'siswa';

    public function label(): string
    {
        return match ($this) {
            self::KepalaSekolah => 'Kepala Sekolah',
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
            'admin', 'kepala_sekolah' => self::KepalaSekolah,
            'guru' => self::Guru,
            'siswa', 'orangtua' => self::Siswa,
            default => self::Guru,
        };
    }
}

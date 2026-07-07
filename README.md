# LMS SMA Islam Assyafiiyah 02 Bekasi

Learning Management System (LMS) resmi untuk SMA Islam Assyafiiyah 02 Bekasi. Dibangun dengan Laravel 11, sistem ini mendigitalisasi aktivitas akademis sekolah: manajemen materi, tugas, ujian online, raport digital, presensi, dan portal orang tua.

## Fitur Utama

- **Multi-Role Access Control**: Admin, Guru, Siswa, Kepala Sekolah, dan Orang Tua
- **E-Learning**: Materi (PDF, PPT, video, link), tugas, dan quiz online
- **Presensi**: Input manual, QR kelas, dan scanner kartu pelajar
- **Raport Digital**: Pembangkitan nilai otomatis dari tugas dan ujian
- **Portal Orang Tua**: Monitoring kehadiran, raport, dan pengumuman
- **Laporan**: Export PDF dan Excel
- **Notifikasi WhatsApp**: Integrasi Fonnte API untuk absensi alfa

## Tech Stack

- **Backend**: Laravel 11 (PHP 8.3+)
- **Frontend**: Blade, Bootstrap 5, Chart.js
- **Database**: MySQL / MariaDB
- **Tools**: DomPDF, Maatwebsite Excel, Simple QR Code

## Instalasi

```bash
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

## Akun Demo (setelah seed)

| Role | Username | Password |
|------|----------|----------|
| Kepala Sekolah | kepsek | kepsek123 |
| Guru | siti.rahayu | guru123 |
| Siswa | 2025001 | 2025001 |

## Dokumentasi Sprint

- `docs/SPRINT-2-CHANGELOG.md` — Rebranding & refactoring
- `docs/SPRINT-3-CHANGELOG.md` — Autentikasi & otorisasi (3 role)
- `docs/SPRINT-4-CHANGELOG.md` — Master data akademik (7 modul)

---
LMS SMA Islam Assyafiiyah 02 Bekasi © 2026 · Developed by NexDigi System

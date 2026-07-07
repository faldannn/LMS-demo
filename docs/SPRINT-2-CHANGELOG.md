# Sprint 2 — Changelog: Rebranding & Refactoring Awal

**Tanggal:** 26 Juni 2026  
**Tujuan:** Rebrand ke LMS SMA Islam Assyafiiyah 02 Bekasi, refactoring struktur kode, optimasi route/middleware/query.

---

## Ringkasan Perubahan

| Kategori | Jumlah File |
|----------|-------------|
| Config & Environment | 4 |
| Database (migration + seeders) | 5 |
| Services (baru + update) | 4 |
| Controllers (refactor) | 5 |
| Models & Providers | 3 |
| Routes | 1 |
| Views (rebrand) | 8 |
| Tests | 1 |
| Assets (logo + favicon) | 2 |
| Dokumentasi | 2 |

---

## 1. Rebranding Identitas

### File Diubah

| File | Perubahan |
|------|-----------|
| `config/school.php` | Identitas sekolah, sistem, warna brand hijau |
| `config/app.php` | Default `APP_NAME` |
| `.env.example` | Variabel sekolah, brand, Fonnte, attendance |
| `database/seeders/SettingSeeder.php` | Data sekolah Assyafiiyah |
| `database/seeders/UserSeeder.php` | Email domain `@assyafiiyah02bekasi.sch.id` |
| `database/seeders/TeacherSeeder.php` | Email domain sekolah |
| `resources/views/layouts/app.blade.php` | Title, meta, favicon, logo, footer, warna hijau |
| `resources/views/auth/login.blade.php` | Full rebrand halaman login |
| `resources/views/dashboard/index.blade.php` | Hero section tema hijau |
| `resources/views/parent-portal/dashboard.blade.php` | Fix link raport orang tua |
| `resources/views/report-cards/print.blade.php` | Fallback nama sekolah |
| `resources/views/reports/pdf-template.blade.php` | Fallback nama sekolah |
| `resources/views/students/print-card.blade.php` | Fallback nama sekolah |
| `README.md` | Dokumentasi proyek baru |
| `public/favicon.svg` | **Baru** — placeholder favicon hijau |
| `public/images/logo-placeholder.svg` | **Baru** — placeholder logo |

### Warna Brand

| Token | Nilai |
|-------|-------|
| Primary | `#15803d` |
| Primary Dark | `#166534` |
| Background | `#f0fdf4` |

Bootstrap `--bs-primary` di-override di layout agar `btn-primary`, `bg-primary`, dll. otomatis hijau.

---

## 2. Refactoring Service Layer

### `app/Services/QrAttendanceService.php` (BARU)

Business logic dipindahkan dari `QrAttendanceController` (207 → ~120 baris):

- `resolveStudentFromQr()` — decode QR kartu pelajar
- `recordAttendance()` — catat kehadiran (unified untuk card scan & class scan)
- `generateClassQrString()` — generate QR kelas
- `parseClassQrPayload()` — parse QR kelas
- `getClassesForQrGeneration()` — daftar kelas per role
- `getLiveAttendanceData()` — data polling monitor

### `app/Services/ParentPortalService.php` (BARU)

Business logic dipindahkan dari `ParentPortalController`:

- `resolveLinkedStudent()` — resolve siswa dari akun orang tua
- `getAnnouncementsForStudent()` — query pengumuman dengan grouping benar
- `getLatestPublishedReportCard()` — raport terakhir
- `getAttendanceRecap()` — rekap absensi via `attendanceDetails` (fix bug)
- `canViewReportCard()` — authorization check

### `app/Services/ReportCardService.php` (UPDATE)

- Fix bug: `$student->attendances` → `$student->attendanceDetails`
- Ekstrak `getClassGradeAverages()` dari `DashboardController`

---

## 3. Optimasi Route & Middleware

### `routes/web.php` (REWRITE)

**Sebelum:** Duplikasi route dashboard & chart (2x), import inline `\App\Http\Controllers\...`

**Sesudah:**
- Import controller eksplisit di bagian atas
- Satu definisi dashboard + chart routes
- Chart endpoints dilindungi `role:admin,guru,kepala_sekolah`
- Route parent portal raport: `parent.report-cards.show`
- Konsistensi penamaan quiz routes (`student/quizzes` tanpa leading slash ganda)

---

## 4. Bug Fixes

| Bug | Fix | File |
|-----|-----|------|
| Role `orangtua` tidak di DB enum | Migration + update migration awal | `2026_06_26_100000_...`, `create_users_table` |
| `Student::attendances()` tidak ada | Gunakan `attendanceDetails` | `ReportCardService`, `ParentPortalService` |
| Parent portal announcement query bocor | Wrap `where` + `orWhere` dalam closure | `ParentPortalService` |
| Parent portal link raport 403 | Route khusus `parent.report-cards.show` | `routes/web.php`, `ParentPortalController` |
| Password reset tampil plaintext | Hapus password dari flash message | `UserController` |
| Duplikasi route name | Hapus duplikat | `routes/web.php` |

---

## 5. Optimasi Performa

| Area | Sebelum | Sesudah |
|------|---------|---------|
| Settings load | `Setting::all()` setiap request | `Cache::remember('app.settings', 3600)` |
| Attendance create | `Classes::with('students')` semua kelas | `Classes::get(['id','name','grade'])` — siswa di-load per kelas |
| QR class list | `Classes::orderBy()->get()` full columns | `get(['id','name','grade'])` |
| Parent announcements | Query tanpa select columns | `with(['creator:id,name', 'class:id,name'])` |

Cache di-invalidate otomatis saat `Setting::set()` dipanggil.

---

## 6. File yang Diubah (Lengkap)

```
config/app.php
config/school.php
.env.example
README.md
docs/SPRINT-2-CHANGELOG.md

database/migrations/2026_04_01_042011_create_users_table.php
database/migrations/2026_06_26_100000_add_orangtua_role_to_users_table.php
database/seeders/SettingSeeder.php
database/seeders/UserSeeder.php
database/seeders/TeacherSeeder.php

app/Providers/AppServiceProvider.php
app/Models/Setting.php
app/Models/User.php
app/Services/QrAttendanceService.php          [BARU]
app/Services/ParentPortalService.php          [BARU]
app/Services/ReportCardService.php
app/Http/Controllers/QrAttendanceController.php
app/Http/Controllers/ParentPortalController.php
app/Http/Controllers/DashboardController.php
app/Http/Controllers/AttendanceController.php
app/Http/Controllers/UserController.php
app/Http/Requests/StoreUserRequest.php
app/Http/Requests/UpdateUserRequest.php

routes/web.php

resources/views/layouts/app.blade.php
resources/views/auth/login.blade.php
resources/views/dashboard/index.blade.php
resources/views/parent-portal/dashboard.blade.php
resources/views/report-cards/print.blade.php
resources/views/reports/pdf-template.blade.php
resources/views/students/print-card.blade.php

public/favicon.svg                              [BARU]
public/images/logo-placeholder.svg              [BARU]

tests/Feature/AuthTest.php
```

---

## 7. Cara Integrasi

### Deploy ke environment existing

```bash
# 1. Pull perubahan
git pull

# 2. Install dependencies (jika ada perubahan)
composer install

# 3. Jalankan migration baru (role orangtua)
php artisan migrate

# 4. Clear cache config & settings
php artisan config:clear
php artisan cache:clear

# 5. Update data sekolah di database (opsional — via seeder atau UI Settings)
php artisan db:seed --class=SettingSeeder

# 6. Pastikan asset publik tersedia
# favicon.svg dan images/logo-placeholder.svg sudah di public/
```

### Update `.env` production

```env
APP_NAME="LMS SMA Islam Assyafiiyah 02 Bekasi"
SCHOOL_NAME="SMA Islam Assyafiiyah 02 Bekasi"
SCHOOL_ADDRESS="Jl. [Alamat Lengkap], Bekasi, Jawa Barat"
SCHOOL_EMAIL="info@assyafiiyah02bekasi.sch.id"
SYSTEM_NAME="LMS SMA Islam Assyafiiyah 02 Bekasi"
SYSTEM_SHORT="LMS Assyafiiyah"
BRAND_PRIMARY="#15803d"
```

### Ganti logo & favicon

1. Ganti `public/images/logo-placeholder.svg` dengan logo sekolah (format SVG/PNG)
2. Ganti `public/favicon.svg` dengan favicon sekolah
3. Atau set path custom di `.env` / `config/school.php` → `brand.logo` dan `brand.favicon`

---

## 8. Cara Testing

### Automated Tests

```bash
php artisan test
```

**Hasil:** 19 tests, 45 assertions — semua PASS.

### Manual Testing Checklist

| # | Test | Expected |
|---|------|----------|
| 1 | Buka `/login` | Title "Login – LMS Assyafiiyah", logo hijau, nama sekolah benar |
| 2 | Login sebagai admin | Dashboard tampil, sidebar hijau, footer benar |
| 3 | Buka Settings | Nama sekolah = Assyafiiyah (atau dari DB) |
| 4 | Scan QR absensi (siswa) | Tetap berfungsi normal |
| 5 | Scanner kartu (guru) | Tetap berfungsi normal |
| 6 | Generate raport | Tidak error (fix attendanceDetails) |
| 7 | Parent portal → Unduh raport | Tidak 403 |
| 8 | Chart dashboard (admin) | Data tampil |
| 9 | Chart dashboard (siswa) | 403 Forbidden |
| 10 | Reset password user | Tidak tampil plaintext di flash |

### Verifikasi Route

```bash
php artisan route:list --name=dashboard
php artisan route:list --name=parent
```

---

## 9. TODO Sprint Berikutnya (Sprint 3)

### Prioritas Tinggi — Keamanan
- [ ] Implementasi Laravel Policy (`MaterialPolicy`, `AssignmentPolicy`, `QuizPolicy`)
- [ ] Fix IDOR student portal (scope assignment/quiz ke kelas siswa)
- [ ] Rate limiting pada QR scan & quiz autosave
- [ ] Aktifkan QR timestamp validation (replay protection)

### Prioritas Sedang — Audit & Log
- [ ] Migration `login_logs` + listener pada login/logout
- [ ] Migration `activity_logs` (atau `spatie/laravel-activitylog`)
- [ ] Session management UI

### Prioritas Sedang — Database
- [ ] Tabel `majors` (jurusan) + FK dari `classes`
- [ ] Link `schedules.subject_id` → `subjects`
- [ ] Unifikasi data orang tua (single source of truth)
- [ ] DB indexes pada query e-learning

### Prioritas Rendah — UI
- [ ] Migrasi warna biru hardcoded di view individual ke CSS variables
- [ ] Migrasi Tailwind CSS (layout → modul per modul)
- [ ] Komponen Blade reusable (`x-card`, `x-stat`, dll.)

### Fitur Baru
- [ ] GPS presensi + geofence settings
- [ ] Forum diskusi
- [ ] Essay quiz grading UI
- [ ] Student announcements page

---

## 10. Catatan Teknis

- **Tidak ada fitur yang dihapus** — semua modul existing tetap berjalan
- **Backward compatible** — QR legacy (`LMS-`, `SIABSEN-`) tetap didukung
- **Tailwind/Vite** tetap terpasang di `package.json` untuk migrasi UI di sprint mendatang; saat ini UI masih Bootstrap 5 dengan override warna hijau
- **SQLite dev:** Migration `orangtua` role menggunakan ALTER ENUM khusus MySQL; SQLite menyimpan role sebagai string sehingga kompatibel tanpa ALTER

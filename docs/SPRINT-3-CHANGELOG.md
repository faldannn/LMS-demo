# Sprint 3 — Changelog: Autentikasi & Otorisasi Produksi

**Tanggal:** 26 Juni 2026  
**Tujuan:** Sistem autentikasi dan otorisasi siap produksi dengan 3 role utama, middleware, policy, dashboard & sidebar dinamis, login/logout history, serta testing.

---

## Ringkasan Perubahan

| Kategori | Jumlah File |
|----------|-------------|
| Migration (baru) | 3 |
| Model (baru) | 2 |
| Enum | 1 |
| Service (baru) | 3 |
| Policy (baru) | 5 |
| Middleware | 1 |
| Controller (update) | 6+ |
| Seeder (baru + update) | 4 |
| Views (sidebar, dashboard, users, login) | 10+ |
| Tests (baru + update) | 5 |
| Dokumentasi | 1 |

---

## 1. Tiga Role Utama

Role disatukan menjadi **3 nilai** di database dan aplikasi:

| Role | Value DB | Akses Utama |
|------|----------|-------------|
| Kepala Sekolah | `kepala_sekolah` | Master data, pengaturan, laporan, e-learning |
| Guru | `guru` | Absensi, e-learning, jadwal, raport |
| Siswa | `siswa` | Portal siswa, materi, tugas, kuis, raport |

### Migrasi Role Lama

| Role Lama | Role Baru |
|-----------|-----------|
| `admin` | `kepala_sekolah` |
| `orangtua` | `siswa` |

Portal orang tua dinonaktifkan sementara di `routes/web.php` (kode tetap ada untuk pengembangan berikutnya).

### File Terkait

| File | Perubahan |
|------|-----------|
| `app/Enums/UserRole.php` | **Baru** — enum 3 role + label + normalisasi legacy |
| `app/Models/User.php` | Cast `UserRole`, helper `isKepalaSekolah()`, `isGuru()`, `isSiswa()`, `isStaff()` |
| `database/migrations/2026_04_01_042011_create_users_table.php` | Enum hanya 3 role |
| `database/migrations/2026_06_26_200002_consolidate_user_roles_to_three.php` | **Baru** — migrasi data + alter enum |

---

## 2. Middleware Role

`RoleMiddleware` memvalidasi role user sebelum mengakses route group.

```php
// bootstrap/app.php
'role' => RoleMiddleware::class,

// routes/web.php — contoh
Route::middleware('role:kepala_sekolah')->group(function () { ... });
Route::middleware('role:guru,kepala_sekolah')->group(function () { ... });
```

Semua route sensitif (master data, settings, chart admin, e-learning guru) dilindungi `role:` middleware.

---

## 3. Authorization — Policy

Policy Laravel untuk kontrol akses per-resource (IDOR protection):

| Policy | Model | Cakupan |
|--------|-------|---------|
| `MaterialPolicy` | `Material` | view, create, update, delete |
| `AssignmentPolicy` | `Assignment` | view, create, update, delete, grade, submit |
| `QuizPolicy` | `Quiz` | view, create, update, delete, attempt |
| `ClassSubjectPolicy` | `ClassSubject` | view, manage |
| `UserPolicy` | `User` | viewAny, create, update, delete |

Trait `ManagesClassSubjectAccess` dipakai bersama untuk cek guru ↔ kelas/mapel.

`app/Http/Controllers/Controller.php` ditambah `AuthorizesRequests` agar `$this->authorize()` tersedia di semua controller.

Controller yang sudah memakai policy: `MaterialController`, `StudentAssignmentController`, `StudentQuizController` (partial).

---

## 4. Dashboard Berbeda per Role

`DashboardService` menyiapkan data berbeda per role. `DashboardController` mengirim variabel `$roleView`:

| `$roleView` | Role | Konten Dashboard |
|-------------|------|------------------|
| `kepsek` | Kepala Sekolah | Statistik sekolah, chart absensi, ringkasan akademik |
| `guru` | Guru | Kelas diampu, tugas/kuis pending, absensi hari ini |
| `siswa` | Siswa | Absensi pribadi, jadwal, tugas/kuis aktif, pengumuman |

View tunggal: `resources/views/dashboard/index.blade.php` dengan blok `@if ($roleView === '...')`.

---

## 5. Sidebar Dinamis

Sidebar dipisah ke partial:

`resources/views/layouts/partials/sidebar-nav.blade.php`

- **Siswa:** absensi, kartu QR, scan, jadwal, materi, tugas, kuis, raport
- **Guru:** absensi, e-learning, jadwal, laporan, raport, pengumuman
- **Kepala Sekolah:** semua menu guru + master data (siswa, guru, kelas, mapel, user, pengaturan)

Di-include dari `resources/views/layouts/app.blade.php`.

---

## 6. Redirect Otomatis Setelah Login

`AuthService::resolveRedirectPath()` mengarahkan semua role ke `route('dashboard')` dengan `redirect()->intended()` di `AuthController` — user yang mencoba akses halaman terproteksi akan kembali ke URL asal setelah login.

---

## 7. Seeder Role

`database/seeders/RoleSeeder.php` menyimpan daftar role ke `settings` key `system_roles` via `Setting::set()`.

Dipanggil dari `DatabaseSeeder`.

---

## 8. Seeder User Dummy

| Seeder | Isi |
|--------|-----|
| `UserSeeder` | `kepsek` + 3 guru (tanpa akun `admin`) |
| `DummyUserSeeder` | Sinkronisasi akun siswa dari data `students` (username = NIS, password = NIS) |

### Kredensial Demo

| Role | Username | Password |
|------|----------|----------|
| Kepala Sekolah | `kepsek` | `kepsek123` |
| Guru | `siti.rahayu` | `guru123` |
| Siswa | `2025001` (NIS) | sama dengan NIS |

Tombol quick-login di halaman login diperbarui (tanpa `admin`/`admin123`).

---

## 9. Optimasi Login

`AuthService::attemptLogin()`:

- Validasi input
- **Rate limiting** 5 percobaan / menit per username+IP
- Cek password hash + status aktif
- **Session regenerate** setelah sukses
- Update `last_login`
- Catat **login log** (sukses/gagal)
- Catat **activity log** pada login sukses
- Route POST `/login` ditambah `throttle:10,1`

---

## 10. Optimasi Logout

`AuthService::logout()`:

- Catat activity log logout
- `Auth::logout()`
- `session()->invalidate()` + `regenerateToken()`
- Redirect ke login dengan flash success

---

## 11. Login History

### Migration

`2026_06_26_200000_create_login_logs_table.php`

| Kolom | Keterangan |
|-------|------------|
| `user_id` | Nullable (gagal login tanpa user valid) |
| `username` | Username yang dicoba |
| `ip_address`, `user_agent` | Metadata request |
| `status` | `success` / `failed` |
| `failure_reason` | `invalid_credentials`, `inactive_account`, dll. |
| `logged_at` | Timestamp |

### Model

`app/Models/LoginLog.php` — relasi `belongsTo(User)`.

---

## 12. Activity Log

### Migration

`2026_06_26_200001_create_activity_logs_table.php`

| Kolom | Keterangan |
|-------|------------|
| `user_id` | User yang melakukan aksi |
| `action` | `login`, `logout`, dll. |
| `description` | Detail opsional |
| `ip_address`, `user_agent` | Metadata |
| `properties` | JSON metadata tambahan |

### Service

`app/Services/ActivityLogService.php` — helper `log()`, `logLogin()`, `logLogout()`.

Model: `app/Models/ActivityLog.php`.

---

## Testing

| File | Cakupan |
|------|---------|
| `tests/Feature/AuthTest.php` | Login/logout — role `kepala_sekolah` |
| `tests/Feature/LoginHistoryTest.php` | **Baru** — login log sukses/gagal, activity log logout |
| `tests/Feature/RoleAuthorizationTest.php` | **Baru** — forbidden untuk siswa/guru, akses kepala sekolah |
| `tests/Feature/AttendanceTest.php` | Factory `kepalaSekolah()` |
| `tests/Feature/StudentTest.php` | Factory `kepalaSekolah()` |

```bash
php artisan test
# 26 passed (58 assertions)
```

---

## Cara Menjalankan

```bash
# Migrasi baru
php artisan migrate

# Seed ulang (opsional, hati-hati di produksi)
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=DummyUserSeeder

# Atau full seed
php artisan migrate:fresh --seed
```

---

## Catatan Teknis / Debt Sprint Berikutnya

1. **Policy belum di semua controller** — `AssignmentController`, `QuizController`, `QuestionController` masih perlu `$this->authorize()` penuh.
2. **Portal orang tua** — route dikomentari; perlu desain ulang jika role orang tua diaktifkan kembali.
3. **Dashboard terpisah** — `AuthService::dashboardViewFor()` mendefinisikan view `dashboard.siswa/guru/kepala-sekolah` tetapi belum dipakai; saat ini satu view dengan `$roleView`.
4. **UI login history / activity log** — data tercatat di DB; halaman admin untuk melihat log belum dibuat.

---

## File Baru (Referensi Cepat)

```
app/Enums/UserRole.php
app/Models/LoginLog.php
app/Models/ActivityLog.php
app/Services/AuthService.php
app/Services/ActivityLogService.php
app/Services/DashboardService.php
app/Policies/MaterialPolicy.php
app/Policies/AssignmentPolicy.php
app/Policies/QuizPolicy.php
app/Policies/ClassSubjectPolicy.php
app/Policies/UserPolicy.php
app/Policies/Concerns/ManagesClassSubjectAccess.php
database/migrations/2026_06_26_200000_create_login_logs_table.php
database/migrations/2026_06_26_200001_create_activity_logs_table.php
database/migrations/2026_06_26_200002_consolidate_user_roles_to_three.php
database/seeders/RoleSeeder.php
database/seeders/DummyUserSeeder.php
resources/views/layouts/partials/sidebar-nav.blade.php
tests/Feature/LoginHistoryTest.php
tests/Feature/RoleAuthorizationTest.php
```

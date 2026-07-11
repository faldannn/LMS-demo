# Sprint 4 — Changelog: Master Data Akademik

**Tanggal:** 26 Juni 2026  
**Tujuan:** CRUD lengkap untuk 7 modul master data akademik dengan service layer, repository, export/import, soft delete, dan testing.

---

## Modul yang Dibangun

| Modul | Route | Import Excel |
|-------|-------|--------------|
| Guru | `teachers.*` | ✅ |
| Siswa | `students.*` | ✅ |
| Kelas | `classes.*` | ✅ |
| Jurusan | `majors.*` | ✅ |
| Mata Pelajaran | `subjects.*` | ✅ |
| Tahun Ajaran | `academic-years.*` | — |
| Semester | `semesters.*` | — |

Semua modul mendukung: **CRUD**, **pagination**, **search/filter**, **soft delete**, **restore**, **export Excel**, **export PDF**.

---

## Arsitektur Baru

```
Controller (Resource + HandlesMasterDataActions trait)
    └── Service (BaseMasterDataService)
            └── Repository (BaseRepository)
```

| Layer | File |
|-------|------|
| Trait | `app/Http/Controllers/Concerns/HandlesMasterDataActions.php` |
| Base Service | `app/Services/MasterData/BaseMasterDataService.php` |
| Base Repository | `app/Repositories/BaseRepository.php` |
| Base Export | `app/Exports/MasterData/BaseMasterDataExport.php` |
| Route helper | `routes/master-data.php` → `registerMasterDataRoutes()` |
| UI Component | `resources/views/components/master-data-toolbar.blade.php` |

---

## Migration Baru

| File | Isi |
|------|-----|
| `2026_06_27_100000_create_majors_table` | Tabel `majors` (jurusan) |
| `2026_06_27_100001_create_semesters_table` | Tabel `semesters` |
| `2026_06_27_100002_add_soft_deletes_to_master_data_tables` | Soft delete: teachers, classes, subjects, academic_years |
| `2026_06_27_100003_refactor_academic_years_and_add_major_id` | Pisah semester dari tahun ajaran; `classes.major_id` FK |

### Perubahan Skema Penting

- **`academic_years`**: hanya `year`, `description`, `is_active` (+ soft delete)
- **`semesters`**: `academic_year_id`, `number` (1/2), `start_date`, `end_date`, `is_active`
- **`classes`**: kolom `major` (string) → `major_id` (FK ke `majors`)

---

## Model Baru / Diperbarui

- **Baru:** `Major`, `Semester`
- **Diperbarui:** `AcademicYear`, `Classes`, `Teacher`, `Subject` (+ SoftDeletes, relasi baru)

---

## Seeder

- `MajorSeeder` — MIPA, IPS, BAHASA, TKJ
- `AcademicYearSeeder` — tahun + 2 semester (migrasi data lama otomatis)
- `ClassSeeder` — menggunakan `major_id`

---

## Route Master Data

Setiap modul memiliki route tambahan (sebelum `Route::resource`):

```
GET  /{module}/trashed
POST /{module}/{id}/restore
DELETE /{module}/{id}/force
GET  /{module}/export/excel
GET  /{module}/export/pdf
POST /{module}/import          (jika didukung)
```

---

## Testing

| File | Cakupan |
|------|---------|
| `tests/Feature/MasterDataTest.php` | CRUD jurusan, tahun ajaran + semester, soft delete/restore, forbidden guru |
| `tests/Feature/StudentTest.php` | Diperbarui untuk skema AcademicYear baru |
| `tests/Feature/AttendanceTest.php` | Diperbarui untuk skema AcademicYear baru |

```bash
php artisan test
# 30 passed (76 assertions)
```

---

## Cara Menjalankan

```bash
php artisan migrate
php artisan db:seed   # atau migrate:fresh --seed
```

---

## Sidebar (Kepala Sekolah)

Menu Data Master diperluas: Siswa, Guru, Kelas, **Jurusan**, Mapel, **Tahun Ajaran**, **Semester**.

---

## Catatan

1. `Semester::active()` menggantikan pola lama `AcademicYear` yang menyimpan semester di baris yang sama.
2. Import Excel membutuhkan header kolom sesuai template di masing-masing `app/Imports/*Import.php`.
3. Halaman trashed tersedia untuk semua 7 modul.

# Sprint 5 — Changelog: Modul LMS Lengkap

**Tanggal:** 26 Juni 2026  
**Tujuan:** Modul pembelajaran daring (LMS) lengkap dengan upload materi multi-format, forum diskusi, komentar, pengumuman, kalender akademik, serta tugas dengan submission, deadline, dan penilaian — semua file melalui **Laravel Storage**.

---

## Ringkasan Fitur

| Fitur | Status | Route Utama |
|-------|--------|-------------|
| Upload PDF | ✅ | `materials.store` |
| Upload PPT/PPTX | ✅ | `materials.store` |
| Upload Video | ✅ | `materials.store` |
| Embed YouTube | ✅ | `materials.store` (type: `youtube`) |
| Download Materi | ✅ | `materials.download` |
| Forum Diskusi | ✅ | `discussions.*` |
| Komentar (polimorfik) | ✅ | `comments.*` |
| Pengumuman + lampiran | ✅ | `announcements.*` |
| Kalender Akademik | ✅ | `academic-calendar.*` |
| Assignment + Deadline | ✅ | `assignments.*` |
| Submission Siswa | ✅ | `student.assignments.*` |
| Penilaian Tugas | ✅ | `assignments.grade` |
| Download Tugas / Submission | ✅ | `assignments.download`, `assignments.submission.download` |

---

## Arsitektur Penyimpanan File

Semua unggahan file menggunakan disk `public` melalui service terpusat:

```
FileStorageService
    ├── upload()    → Storage::disk('public')->store()
    ├── download()  → Storage::disk('public')->download()
    ├── delete()    → Storage::disk('public')->delete()
    └── extractYoutubeId() → parsing URL YouTube
```

| Modul | Direktori Storage | Service |
|-------|-------------------|---------|
| Materi | `materials/{class_subject_id}/` | `MaterialService` |
| Tugas (lampiran guru) | `assignments/{id}/` | `AssignmentService` |
| Submission siswa | `submissions/{assignment_id}/` | `AssignmentService` |
| Pengumuman | `announcements/` | `AnnouncementService` |

Jalankan `php artisan storage:link` agar file dapat diakses publik (preview video) dan diunduh via controller.

---

## Skema Database Baru & Perubahan

### Migration Baru

| File | Tabel | Deskripsi |
|------|-------|-----------|
| `2026_06_28_100000_enhance_materials_for_lms_module` | `materials` | Kolom `youtube_id`, `created_by`; tipe diperluas: `pdf`, `ppt`, `video`, `youtube`, `image`, `link` |
| `2026_06_28_100001_create_discussion_threads_table` | `discussion_threads` | Forum per `class_subject_id` |
| `2026_06_28_100002_create_comments_table` | `comments` | Komentar polimorfik (`commentable_type`, `commentable_id`) + `parent_id` untuk balasan |
| `2026_06_28_100003_create_academic_calendar_events_table` | `academic_calendar_events` | Event kalender dengan kategori |
| `2026_06_28_100004_enhance_announcements_table` | `announcements` | Lampiran file + `is_published` |

### Relasi Utama

```
class_subject
    ├── materials (1:N)
    ├── assignments (1:N)
    ├── discussion_threads (1:N)
    └── quizzes (1:N)

discussion_threads ── morphMany ── comments
materials          ── morphMany ── comments
announcements      ── morphMany ── comments

comments.parent_id → comments (balasan bersarang)
```

### Tipe Materi

| Tipe | Input | Penyimpanan |
|------|-------|-------------|
| `pdf` | File `.pdf` | Storage |
| `ppt` | File `.ppt/.pptx` | Storage |
| `video` | File `mp4/webm/...` | Storage |
| `youtube` | URL YouTube | `url` + `youtube_id` (tanpa file) |
| `image` | File gambar | Storage |
| `link` | URL eksternal | `url` saja |

---

## Layer Aplikasi

### Service Baru / Diperbarui

| File | Fungsi |
|------|--------|
| `app/Services/FileStorageService.php` | Abstraksi upload/download/delete Storage |
| `app/Services/MaterialService.php` | CRUD materi, `prepareData()`, download |
| `app/Services/AssignmentService.php` | Tugas, submission, grading, download file |
| `app/Services/DiscussionService.php` | Daftar & buat topik forum |
| `app/Services/CommentService.php` | Tambah/hapus komentar polimorfik |
| `app/Services/AcademicCalendarService.php` | Event kalender per bulan |
| `app/Services/AnnouncementService.php` | Pengumuman + lampiran Storage |

### Model Baru

- `DiscussionThread` — topik forum (soft delete)
- `Comment` — komentar polimorfik dengan `parent_id`
- `AcademicCalendarEvent` — event kalender akademik

### Model Diperbarui

- `Material` — tipe baru, `youtube_id`, `created_by`, relasi `comments`
- `Announcement` — lampiran file, `is_published`, relasi `comments`

### Controller Baru

| Controller | Method |
|------------|--------|
| `DiscussionController` | `index`, `create`, `store`, `show`, `reply` |
| `CommentController` | `store`, `destroy` |
| `AcademicCalendarController` | `index`, `create`, `store`, `destroy` |

### Controller Diperbarui

- `MaterialController` — `download`, `created_by`, eager-load komentar
- `AssignmentController` — otorisasi policy, `download`, `downloadSubmission`
- `AnnouncementController` — `show`, `download`, integrasi `AnnouncementService`

### Policies Baru

- `DiscussionThreadPolicy` — view/reply berdasarkan akses kelas
- `CommentPolicy` — hapus komentar sendiri atau staff
- `AcademicCalendarEventPolicy` — CRUD event untuk staff

### Form Request Baru

- `StoreCommentRequest`
- `StoreDiscussionThreadRequest`
- `StoreAcademicCalendarEventRequest`
- `StoreAnnouncementRequest`

`StoreMaterialRequest` diperbarui untuk validasi `pdf`, `ppt`, `video`, `youtube`.

---

## Routes

### Shared (semua role terautentikasi)

```
GET  materials/detail/{material}              → materials.show
GET  materials/detail/{material}/download     → materials.download
GET  announcements                            → announcements.index
GET  announcements/{announcement}             → announcements.show
GET  announcements/{announcement}/download    → announcements.download
GET  discussions/{classSubject}                 → discussions.index
GET  discussions/{classSubject}/create          → discussions.create
POST discussions/{classSubject}                 → discussions.store
GET  discussions/thread/{discussion}            → discussions.show
POST discussions/thread/{discussion}/reply      → discussions.reply
POST comments/{type}/{id}                       → comments.store
DELETE comments/{comment}                       → comments.destroy
GET  academic-calendar                          → academic-calendar.index
```

### Staff only (guru / kepala sekolah)

```
POST   academic-calendar
GET    academic-calendar/create
DELETE academic-calendar/{academicCalendar}
POST   announcements (create)
GET    assignments/.../download
GET    assignments/submissions/.../download
```

### Siswa

```
GET  my-materials                    → materials.mine
GET  my-assignments                  → student.assignments.*
POST my-assignments/{assignment}/submit
```

---

## Views Baru / Diperbarui

| View | Perubahan |
|------|-----------|
| `materials/create.blade.php` | Form PDF, PPT, Video, YouTube |
| `materials/edit.blade.php` | Sama seperti create |
| `materials/show.blade.php` | Embed YouTube, download, preview video, komentar |
| `discussions/index.blade.php` | Daftar topik forum |
| `discussions/create.blade.php` | Form topik baru |
| `discussions/show.blade.php` | Detail + balasan |
| `academic-calendar/index.blade.php` | Grid event per bulan |
| `academic-calendar/create.blade.php` | Form event |
| `announcements/show.blade.php` | Detail + lampiran + komentar |
| `announcements/index.blade.php` | Link ke detail, aksi staff-only |
| `partials/comments.blade.php` | Komponen komentar reusable |
| `elearning/index.blade.php` | Tombol Forum per mapel |
| `layouts/partials/sidebar-nav.blade.php` | Link Pengumuman & Kalender Akademik |

---

## Testing

File: `tests/Feature/LmsModuleTest.php`

| Test | Cakupan |
|------|---------|
| Upload PDF via Storage | Guru upload, file tersimpan di disk `public` |
| View & download materi | Siswa akses materi terpublikasi |
| Materi YouTube | Ekstraksi `youtube_id` dari URL |
| Komentar materi | Siswa posting komentar |
| Forum diskusi | Buat topik + balasan guru |
| Kalender akademik | Staff buat event, siswa lihat |
| Deadline tugas | Siswa melihat daftar tugas |

```bash
php artisan test --filter=LmsModuleTest
php artisan test   # 38 tests, 100 assertions
```

---

## Cara Menjalankan

```bash
# Migrasi
php artisan migrate

# Symlink storage
php artisan storage:link

# Seed data (opsional)
php artisan db:seed

# Jalankan server
php artisan serve
```

### Akun Demo

| Role | Username | Password |
|------|----------|----------|
| Kepala Sekolah | `kepsek` | `kepsek123` |
| Guru | `siti.rahayu` | `guru123` |
| Siswa | `2025001` | `2025001` |

### Alur Penggunaan

1. **Guru** → Portal E-Learning → pilih mapel → Materi / Forum / Tugas
2. **Siswa** → Materi Pelajaran / Tugas Saya / Pengumuman / Kalender Akademik
3. **Kepala Sekolah** → Monitoring Pembelajaran + Kalender Akademik

---

## Catatan Pengembangan

- Komentar menggunakan relasi polimorfik sehingga mudah ditambahkan ke entitas baru (mis. `Quiz`, `Assignment`) tanpa migrasi tambahan.
- `discussion_threads.replies_count` disimpan untuk performa daftar forum; dihitung via `withCount('allComments')`.
- Tipe materi lama `document` otomatis dimigrasi ke `pdf`.
- Untuk production, pastikan `FILESYSTEM_DISK=public` atau sesuaikan di `config/filesystems.php`.

# Sprint 8 — Changelog: Modul Laporan Analitik

**Tanggal:** 26 Juni 2026  
**Tujuan:** Enam jenis laporan terpusat dengan filter lengkap dan ekspor PDF/Excel.

---

## Laporan yang Tersedia

| Slug | Judul | Sumber Data |
|------|-------|-------------|
| `teacher-attendance` | Kehadiran Guru | Sesi presensi (`attendance`) per guru |
| `student-attendance` | Kehadiran Siswa | Rekap status per siswa (`attendance_details`) |
| `grades` | Nilai | Rapor (`report_cards` + `report_card_details`) |
| `teacher-activity` | Aktivitas Guru | Materi, tugas, kuis, log aktivitas guru |
| `student-activity` | Aktivitas Siswa | Pengumpulan tugas, kuis, presensi mandiri, log |
| `learning` | Pembelajaran | Konten per kelas–mapel (materi, tugas, kuis, diskusi) |

---

## Filter Bersama

Semua laporan mendukung filter:

- **Tahun Ajaran** (`academic_year_id`)
- **Semester** (`semester_id`) — otomatis mengisi rentang tanggal dari `semesters.start_date` / `end_date`
- **Kelas** (`class_id`)
- **Mata Pelajaran** (`subject_id`)
- **Rentang Tanggal** (`date_from`, `date_to`)

Jika semester dipilih tanpa tanggal manual, rentang tanggal diambil dari semester. Jika hanya tahun ajaran dipilih, rentang diambil dari gabungan semester pada tahun tersebut. Default: bulan berjalan.

---

## Arsitektur

```
ReportFilterRequest (validasi)
        ↓
ReportFilter (resolve rentang tanggal)
        ↓
ComprehensiveReportService (6 metode laporan)
        ↓
ReportController::show / exportPdf / exportExcel
        ↓
Blade (reports/show) + PDF (reports/pdf/generic) + Excel (GenericReportExport)
```

---

## Route

| Method | URI | Nama |
|--------|-----|------|
| GET | `/reports` | `reports.index` |
| GET | `/reports/{type}` | `reports.show` |
| GET | `/reports/{type}/export/pdf` | `reports.type.pdf` |
| GET | `/reports/{type}/export/excel` | `reports.type.excel` |

Laporan absensi klasik (harian/bulanan/rekap) tetap tersedia di bawah `/reports/daily`, `/reports/monthly`, `/reports/recap`.

**Akses:** `kepala_sekolah`, `guru`

---

## File Baru / Diubah

### Backend
- `app/Support/ReportFilter.php`
- `app/Http/Requests/ReportFilterRequest.php`
- `app/Services/Reports/ComprehensiveReportService.php`
- `app/Exports/Reports/GenericReportExport.php`
- `config/reports.php`
- `app/Http/Controllers/ReportController.php` (diperluas)
- `routes/web.php`

### Views
- `resources/views/reports/index.blade.php` (hub 6 laporan)
- `resources/views/reports/show.blade.php`
- `resources/views/reports/partials/filter-form.blade.php`
- `resources/views/reports/pdf/generic.blade.php`
- `resources/views/layouts/partials/sidebar-nav.blade.php` (label "Laporan")

### Tests
- `tests/Feature/AnalyticsReportTest.php` (6 skenario)

---

## Cara Uji Manual

1. Login sebagai kepala sekolah atau guru.
2. Buka menu **Laporan** di sidebar.
3. Pilih salah satu dari 6 kartu laporan analitik.
4. Atur filter (tahun ajaran, semester, kelas, mapel, tanggal).
5. Klik **Terapkan Filter**, lalu **PDF** atau **Excel**.

---

## Hasil Test

```
php artisan test --filter=AnalyticsReportTest
# 6 passed

php artisan test
# 55 passed (157 assertions)
```

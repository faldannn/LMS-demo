# Project Rules & Development Workflow - LMS Assyafiiyah

Setiap pengerjaan tugas, penambahan fitur, ataupun perbaikan bug di repository LMS ini **WAJIB** mematuhi 4 tahap alur kerja baku berikut secara berurutan:

---

## 1. Edit Lokal (Local Development)
- Seluruh modifikasi file (controller, model, view Blade, database migration, dan konfigurasi) harus dikerjakan secara lokal di direktori workspace `c:\laravel10\lms-assyafiiyah`.
- Dilarang keras melakukan hotfix langsung di server produksi tanpa melalui kode lokal.

---

## 2. Testing & Validasi (Local Verification)
- Setiap setelah selesai melakukan perubahan kode, lakukan testing dan validasi terlebih dahulu:
  - Verifikasi sintaks PHP: `php -l <path-to-file>`
  - Jalankan test jika ada unit/feature test terkait (`php artisan test`).
  - Pastikan tidak ada syntax error atau regresi logic.

---

## 3. Git Commit & Push (Version Control)
- Cek status dengan `git status`.
- Stage seluruh perubahan terkait dan buat commit dengan pesan deskriptif:
  ```powershell
  git add -A; git commit -m "<tipe>: <deskripsi perubahan>"
  ```
- Lakukan sinkronisasi dan push ke branch `main`:
  ```powershell
  git pull --rebase origin main; git push origin main
  ```

---

## 4. Deploy ke Production (Automated Deployment)
- Unggah file yang diubah ke server Hostinger (`domains/smaiassyafiiyah02.com/public_html/lms/`).
- Jalankan pembersihan dan pembaruan cache Laravel di server:
  - `php artisan config:cache`
  - `php artisan route:cache`
  - `php artisan view:clear`
- Konfirmasi dan verifikasi fungsionalitas live berjalan normal.

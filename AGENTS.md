# Project Rules & Development Workflow - LMS Demo Nexus (Demo System)

Project folder ini adalah **LMS Demo System** yang berdiri sendiri (standalone) untuk kebutuhan showcase / portfolio CP Nexus.

---

## 1. Lingkungan Mandiri (Standalone Environment)
- **Terputus Total dari Server Produksi**: DILARANG mengunggah, menghubungkan, atau menyinkronkan file ke server hosting produksi Hostinger (`domains/smaiassyafiiyah02.com/public_html/lms/`) atau server hosting lainnya.
- **Terputus dari GitHub Utama**: Project ini telah diputus dari remote repository `NexusDevWeb/lms-assyafiiyah.git`.
- File perubahan hanya disimpan dan dikembangkan secara lokal di folder `c:\laravel10\Copy an\lms-assyafiiyah-copy` sampai repository GitHub baru untuk demo disiapkan.

---

## 2. Testing & Validasi Lokal
- Verifikasi sintaks PHP: `php -l <path-to-file>`
- Jalankan test jika ada unit/feature test: `php artisan test`
- Pastikan tampilan dan fitur demo berjalan optimal tanpa ketergantungan API/server live.

---

## 3. Version Control (Repository Demo LMS)
- Remote repository demo resmi: `https://github.com/NexusDevWeb/LMS-Demo.git`
- **Persetujuan Push Wajib**: DILARANG melakukan `git push` secara otomatis. `git push` HANYA boleh dijalankan setelah mendapatkan persetujuan eksplisit dari pengguna.
- Alur kerja commit & push saat diminta/disetujui:
  ```powershell
  git add -A
  git commit -m "<tipe>: <deskripsi perubahan>"
  # Push HANYA setelah persetujuan pengguna:
  git push origin main
  ```


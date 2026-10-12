# 🎓 Nexus LMS (Learning Management System)
### Modern Digital Learning & School Management Platform
**Official Showcase & Demo System — Nexus Portfolio**

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Status](https://img.shields.io/badge/Status-Demo%20%26%20Showcase-success?style=for-the-badge)](#)

---

## 🌟 Tentang Nexus LMS Demo

**Nexus LMS** adalah platform manajemen pembelajaran dan administrasi sekolah digital terintegrasi (*All-in-One E-Learning & Academic System*) kelas *enterprise* yang dikembangkan oleh **Nexus Dev Team**. 

Sistem ini dirancang khusus sebagai showcase dan demonstrasi solusi teknologi terpadu untuk institusi pendidikan modern. Seluruh proses akademik—mulai dari manajemen materi, Computer-Based Test (CBT), presensi berbasis Geofencing & QR, hingga penerbitan raport digital otomatis—dapat dikelola secara terpusat, transparan, dan real-time.

---

## 🎯 Visi & Solusi

- **Transformasi Digital Holistik**: Menggantikan alur manual dan administrasi kertas menjadi alur digital terpadu yang efisien dan minim kesalahan.
- **Kolaborasi Multi-Stakeholder**: Menyediakan antarmuka khusus yang dirancang sesuai kebutuhan spesifik Kepala Sekolah, Guru, Siswa, Orang Tua, dan Administrator.
- **Akuntabilitas & Keamanan Berstandar Tinggi**: Menyajikan data akademik yang akurat dengan audit logging lengkap, proteksi anti-kecurangan, dan kontrol akses berbasis peran (RBAC).

---

## ✨ Fitur Unggulan

### 📚 1. Digital Learning Suite
- **Multi-Format Materi**: Dukungan dokumen PDF, PPT, video streaming, integrasi YouTube player, audio, dan tautan referensi interaktif.
- **Manajemen Tugas Cerdas**: Pengumpulan tugas online, pembatasan deadline otomatis, rubrik penilaian terstandar, dan feedback langsung dari guru.
- **Forum Diskusi Interaktif**: Ruang diskusi per mata pelajaran dengan threaded comments dan moderasi guru.
- **Kalender Akademik Terpadu**: Sinkronisasi jadwal ujian, kegiatan sekolah, dan agenda akademik.

### 👥 2. Presensi & Monitoring Pintar
- **Multi-Mode Attendance**: Dukungan presensi via QR Code scanner, Self Check-in, Geofencing radius GPS sekolah, dan input manual oleh pengajar.
- **Notifikasi Otomatis (WhatsApp Integration)**: Pemberitahuan kehadiran siswa langsung ke nomor WhatsApp orang tua/wali secara real-time.
- **Analytics & Trend Kehadiran**: Rekap persentase kehadiran per siswa, kelas, maupun rekap bulanan dalam bentuk visual chart.

### 📊 3. Ujian Online (CBT) & Raport Digital
- **Computer-Based Testing (CBT)**: Ujian online dengan bank soal acak, timer otomatis, dan deteksi kecurangan (tab switching/blur detection).
- **Automated Grading Engine**: Koreksi otomatis untuk soal objektif dan formulir grading terstruktur untuk soal esai.
- **E-Raport Otomatis**: Generator nilai akhir dan raport digital otomatis berformat PDF siap cetak dengan template profesional.
- **Export Laporan Eksekutif**: Ekspor rekapitulasi data akademik ke format Excel dan PDF sekali klik.

### 🛡️ 4. Keamanan & Kontrol Administrator
- **Role-Based Access Control (RBAC)**: Pemisahan hak akses ketat antara Kepala Sekolah, Guru, Siswa, dan Super Admin.
- **Security Audit Logs**: Pencatatan riwayat setiap aksi sistem (CRUD, login IP, waktu, user agent).
- **Session & Device Monitor**: Monitoring sesi aktif untuk mencegah akun digunakan bersamaan.
- **Automated Backup**: Utilitas backup basis data berkala yang andal.

---

## 🏗️ Arsitektur Teknis

### Tech Stack
| Komponen | Spesifikasi / Teknologi |
|---|---|
| **Backend Framework** | Laravel 11 (PHP 8.3+) |
| **Frontend UI** | Blade Templating Engine, Bootstrap 5.3, Vite |
| **Data Visualization** | Chart.js & ApexCharts |
| **Database** | MySQL 8.0+ / MariaDB 10.6+ |
| **Asynchronous Engine** | Laravel Queue Worker (Database / Redis) |
| **Caching System** | Laravel Cache (File / Database / Redis) |
| **File Storage** | Laravel Storage Abstraction (Local / Cloud Object Storage) |

### Library & Integrasi Utama
- **PDF Engine**: DomPDF (cetak raport, kartu ujian, rekap nilai)
- **Spreadsheet Integration**: Maatwebsite Excel (impor/ekspor data siswa, guru, nilai)
- **QR Code Generator**: Simple QR Code
- **WhatsApp Notification**: Fonnte API Gateway

---

## 🚀 Panduan Instalasi Lokal (Demo Setup)

### Kebutuhan Sistem (Prerequisites)
- PHP >= 8.3 (Ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `gd`, `zip`, `bcmath`, `fileinfo`)
- Composer >= 2.x
- Node.js >= 18.x & NPM
- MySQL 8.0+ atau MariaDB 10.6+
- Git

### Langkah Menjalankan Demo
```bash
# 1. Clone repository demo
git clone https://github.com/NexusDevWeb/LMS-Demo.git
cd LMS-Demo

# 2. Pasang dependensi PHP & Javascript
composer install
npm install && npm run build

# 3. Siapkan file konfigurasi lingkungan (.env)
cp .env.example .env
php artisan key:generate

# 4. Sesuaikan konfigurasi database pada file .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_demo
DB_USERNAME=root
DB_PASSWORD=

# 5. Jalankan migrasi tabel dan data demo (dummy seeder)
php artisan migrate --seed
php artisan storage:link

# 6. Jalankan server lokal
php artisan serve
```

Aplikasi dapat diakses melalui browser di: `http://localhost:8000`

---

## 👤 Hak Akses & Peran Pengguna (Roles)

| Role | Deskripsi & Tanggung Jawab Utama |
|---|---|
| 👑 **Kepala Sekolah** | Akses dashboard analitik tingkat tinggi, pemantauan performa akademik sekolah, monitoring absensi seluruh kelas, evaluasi capaian kurikulum, dan pengesahan raport. |
| 👨‍🏫 **Guru / Pengajar** | Manajemen mata pelajaran, publikasi modul/materi belajar, perumusan tugas & kuis, absensi kelas, grading terpusat, dan diskusi kelas. |
| 🎓 **Siswa** | Portal belajar mandiri, akses materi & penugasan, presensi mandiri (self check-in), pengerjaan CBT kuis/ujian, serta rekap nilai pribadi. |
| ⚙️ **Administrator** | Manajemen data master (Tahun Ajaran, Rombel, Pengguna), pengaturan sistem global, backup & restore database, serta pemantauan log audit keamanan. |

---

## 📁 Struktur Data & Skema Database

Dirancang dengan **50+ file migration modular** yang mengikuti kaidah normalisasi database relasional:

```text
├── Core System
│   ├── users (Super Admin, Kepala Sekolah, Guru, Siswa)
│   ├── academic_years, semesters, majors
│   └── settings, audit_logs, login_logs
│
├── Academic Management
│   ├── classes, subjects, class_subject (Pivot)
│   ├── teachers, students, student_parents
│   └── schedules, academic_calendar_events
│
├── Learning Management
│   ├── materials (PDF, Video, PPT, YouTube Link, Images)
│   ├── assignments, assignment_submissions, assignment_grades
│   ├── quizzes, questions, quiz_attempts, student_answers
│   └── discussion_threads, comments (Polymorphic)
│
├── Attendance & Tracking
│   ├── attendance, attendance_details
│   └── self_attendance_logs (GPS & QR Verification)
│
└── Evaluation & Assessment
    ├── report_cards, report_card_details
    └── question_banks, quiz_questions
```

---

## 🔐 Keamanan & Kepatuhan Data

- **Proteksi Akses (RBAC & Policy)**: Validasi otorisasi di level routing, controller, hingga view template.
- **Perlindungan Injeksi & Serangan**: Menggunakan Eloquent PDO prepared statement (anti-SQL Injection), token CSRF otomatis, sanitasi XSS pada Blade, serta middleware rate limiting anti-DDoS/brute force.
- **Audit Trail Menyeluruh**: Setiap operasi mutasi data sensitif tercatat rapi pada tabel audit log.

---

## 🧪 Validasi & Automated Testing

Proyek ini telah dilengkapi automated testing untuk menjamin keandalan fungsionalitas modul:

```bash
# Menjalankan seluruh test suite
php artisan test

# Menjalankan pengujian spesifik
php artisan test --filter=AuthenticationTest
php artisan test --filter=LmsModuleTest
php artisan test --filter=ProductionSecurityTest
```

---

## 💼 Nexus Portfolio & Showcase Information

- **Pengembang**: **CP Nexus (Nexus Dev Team)**
- **Kategori**: Enterprise EdTech & Learning Management System
- **Repository Showcase**: [https://github.com/NexusDevWeb/LMS-Demo.git](https://github.com/NexusDevWeb/LMS-Demo.git)
- **Tujuan**: Demonstrasi solusi sistem terintegrasi, arsitektur perangkat lunak skala enterprise, dan portfolio kapabilitas tim Nexus.

---

## 📄 Lisensi & Hak Penggunaan

© 2026 **Nexus Dev Team**. Seluruh hak cipta dilindungi undang-undang.  
Sistem ini dipublikasikan sebagai materi demonstrasi portofolio teknologi resmi Nexus.
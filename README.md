# Learning Management System (LMS)
## SMA Islam Assyafiiyah 02 Bekasi

**Sistem Manajemen Pembelajaran Digital Terintegrasi** - Portal e-learning terpusat untuk kolaborasi antara guru, siswa, kepala sekolah, dan admin.

---

## 🏫 Tentang Sistem

LMS ini dikembangkan khusus untuk mendigitalisasi seluruh proses akademik SMA Islam Assyafiiyah 02 Bekasi. Sistem ini mengintegrasikan pembelajaran, penilaian, presensi, dan administrasi sekolah dalam satu platform terpusat.

### 🎯 **Visi**
Mewujudkan transformasi digital pendidikan Islami yang terintegrasi, efisien, dan berkualitas.

### ✅ **Misi**
1. Digitalisasi proses pembelajaran dan administrasi akademik
2. Memfasilitasi kolaborasi antar seluruh stakeholder sekolah
3. Meningkatkan transparansi dan akuntabilitas pendidikan
4. Memberikan akses pendidikan yang merata dan berkualitas

---

## ✨ **Fitur Utama**

### 📚 **Pembelajaran Digital**
- **Materi Pembelajaran**: PDF, PPT, Video, YouTube, Link, Image
- **Sistem Tugas**: Upload tugas, deadline management, automated grading
- **Quiz & Ujian Online**: Computer-Based Test (CBT) dengan bank soal
- **Forum Diskusi**: Diskusi kelas dengan sistem komentar berjenjang
- **Kalender Akademik**: Event sekolah dan kegiatan Islami

### 👥 **Manajemen Kehadiran**
- **Multi-Mode Presensi**: Manual, QR Code, Self Check-in, Geofencing
- **Real-time Monitoring**: Dashboard kehadiran per kelas
- **Notifikasi Otomatis**: WhatsApp notifikasi untuk orang tua (Fonnte API)
- **Analytics**: Trend kehadiran dan statistik bulanan

### 📊 **Sistem Penilaian**
- **Penilaian Otomatis**: Kalkulasi nilai dari tugas dan ujian
- **Raport Digital**: Generation otomatis dengan template profesional
- **Grade Analytics**: Distribusi nilai per kelas dan mapel
- **Export Laporan**: PDF dan Excel dengan custom formatting

### 🛡️ **Keamanan & Admin**
- **Multi-Role Access**: 3 role utama (Kepala Sekolah, Guru, Siswa)
- **Audit Logging**: Pencatatan lengkap semua aktivitas sistem
- **Session Management**: Monitor dan kontrol sesi aktif
- **Backup System**: Backup database otomatis dan manual
- **Security Headers**: Proteksi keamanan tingkat enterprise

---

## 🏗️ **Arsitektur Teknis**

### **Tech Stack**
| Komponen | Teknologi |
|----------|-----------|
| **Backend** | Laravel 11 (PHP 8.3+) |
| **Frontend** | Blade Templating, Bootstrap 5, Chart.js |
| **Database** | MySQL 8.0+ / MariaDB 10.6+ |
| **Queue** | Database Queue dengan Supervisor |
| **Caching** | Database / Redis |
| **File Storage** | Laravel Storage (local/S3) |

### **Library Utama**
- **PDF Generation**: DomPDF
- **Excel Export**: Maatwebsite Excel
- **QR Code**: Simple QR Code
- **Charts**: Chart.js
- **Notifications**: WhatsApp API (Fonnte)

---

## 🚀 **Instalasi & Setup**

### **Prerequisites**
- PHP 8.3+ dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `gd`, `zip`, `bcmath`, `fileinfo`
- Composer 2.x
- Node.js 18+ & npm
- MySQL 8.0+ atau MariaDB 10.6+
- Git

### **Langkah Instalasi**
```bash
# 1. Clone repository
git clone <repository-url> lms-assyafiiyah
cd lms-assyafiiyah

# 2. Install dependencies
composer install
npm install && npm run build

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_assyafiiyah
DB_USERNAME=root
DB_PASSWORD=

# 5. Jalankan migrasi dan seeding
php artisan migrate --seed
php artisan storage:link

# 6. Jalankan server development
php artisan serve
```

### **Optimasi Produksi**
```bash
# Cache untuk performa optimal
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Setup queue worker (Supervisor)
php artisan queue:work

# Setup scheduler (Cron)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 👤 **Role & Akses**

### **Kepala Sekolah**
- Dashboard analitik komprehensif
- Monitoring seluruh aktivitas akademik
- Laporan kehadiran dan nilai
- Manajemen sistem dan keamanan

### **Guru**
- Manajemen materi pembelajaran
- Pembuatan tugas dan penilaian
- Input presensi dan monitoring siswa
- Forum diskusi kelas

### **Siswa**
- Portal pembelajaran digital
- Akses materi dan tugas
- Presensi mandiri (self check-in)
- Ujian online dan quiz
- Monitoring nilai dan kehadiran

### **Admin Sistem**
- Manajemen master data
- Backup dan restore database
- Audit logging dan security
- System maintenance

---

## 📁 **Struktur Database**

Sistem menggunakan **50+ migration files** dengan desain database yang terstruktur:

```
┌── Core
│   ├── users (3 roles: kepala_sekolah, guru, siswa)
│   ├── academic_years, semesters, majors
│   └── settings, audit_logs, login_logs
│
├── Academic
│   ├── classes, subjects, class_subject (M2M)
│   ├── teachers, students, student_parents
│   └── schedules, academic_calendar_events
│
├── Learning
│   ├── materials (pdf, ppt, video, youtube, link, image)
│   ├── assignments, assignment_submissions, assignment_grades
│   ├── quizzes, questions, quiz_attempts, student_answers
│   └── discussion_threads, comments (polymorphic)
│
├── Attendance
│   ├── attendance, attendance_details
│   └── self_attendance_logs
│
└── Assessment
    ├── report_cards, report_card_details
    └── question_banks, quiz_questions
```

---

## 🔐 **Keamanan Sistem**

### **Security Features**
- **Role-Based Access Control (RBAC)**: Middleware dan Policies
- **Rate Limiting**: Proteksi terhadap brute force attacks
- **XSS Protection**: Output escaping di semua Blade templates
- **CSRF Protection**: Token pada semua form submissions
- **SQL Injection Prevention**: Eloquent ORM dengan parameter binding
- **File Upload Validation**: MIME type dan size validation
- **Session Security**: Database session dengan encryption

### **Audit & Monitoring**
- **Activity Logs**: Pencatatan semua operasi CRUD
- **Login History**: Riwayat login dengan IP dan device info
- **Security Dashboard**: Monitoring real-time aktivitas sistem
- **Backup Management**: Backup otomatis dan manual

---

## 📈 **Deployment Produksi**

### **Checklist Deployment**
1. ✅ Setup server dengan spesifikasi minimum 2GB RAM
2. ✅ Konfigurasi HTTPS dengan Let's Encrypt
3. ✅ Setup environment production (.env)
4. ✅ Jalankan optimasi cache Laravel
5. ✅ Konfigurasi cron job untuk scheduler
6. ✅ Setup supervisor untuk queue worker
7. ✅ Testing comprehensive semua fitur
8. ✅ Backup database sebelum go-live

### **Monitoring Produksi**
- **Health Check**: `GET /up` endpoint
- **Error Tracking**: Daily error logs
- **Performance**: Query optimization indexes
- **Backup**: Automatic daily backup ke storage

---

## 🧪 **Testing**

### **Test Coverage**
```bash
# Jalankan semua tests
php artisan test

# Test spesifik module
php artisan test --filter=AuthenticationTest
php artisan test --filter=LmsModuleTest
php artisan test --filter=ProductionSecurityTest
```

### **Testing Strategy**
- **Unit Tests**: Business logic dan services
- **Feature Tests**: End-to-end workflow testing
- **Security Tests**: Authentication dan authorization
- **Performance Tests**: Database query optimization

---

## 📚 **Dokumentasi**

### **Dokumentasi Utama**
- `docs/DEPLOYMENT.md` - Panduan deployment produksi
- `docs/ARCHITECTURE.md` - Arsitektur sistem dan database design
- `docs/API_DOCUMENTATION.md` - API endpoints documentation

### **Changelog & Development**
- Sprint documentation tersedia di folder `docs/`
- Git commit history untuk tracking perubahan
- Production readiness checklist di `SPRINT-9-PRODUCTION-READINESS.md`

---

## 🤝 **Kontribusi & Support**

### **Tim Pengembang**
- **NexDigi System** - Development & Maintenance
- **Tim IT Sekolah** - Deployment & Operational Support
- **Stakeholder Sekolah** - Feedback & Feature Requests

### **Support Channels**
- **Technical Issues**: Tim IT sekolah
- **Feature Requests**: Meeting koordinasi bulanan
- **Emergency Support**: WhatsApp group dedicated

### **Update Policy**
- **Minor Updates**: Setiap bulan
- **Major Updates**: Setiap semester
- **Security Patches**: Immediate deployment

---

## 📄 **Lisensi & Hak Cipta**

**LMS SMA Islam Assyafiiyah 02 Bekasi** © 2026  
Dikembangkan oleh **NexDigi System**  
Hak cipta dilindungi undang-undang.

### **Terms of Use**
1. Sistem ini dikhususkan untuk penggunaan internal SMA Islam Assyafiiyah 02 Bekasi
2. Tidak diperbolehkan mendistribusikan atau menggunakan untuk kepentingan komersial
3. Semua data dan konten adalah milik sekolah
4. Compliance dengan regulasi pendidikan dan data privacy

---

## 🏆 **Pencapaian Sistem**

✅ **9,500+ Lines of Code** dengan arsitektur terstruktur  
✅ **50+ Database Migrations** dengan optimasi indexes  
✅ **63+ Automated Tests** dengan coverage komprehensif  
✅ **Production-Ready** dengan security enterprise-grade  
✅ **Multi-Role Dashboard** untuk semua stakeholder  
✅ **Integrated WhatsApp Notifications** untuk orang tua  
✅ **CBT Exam System** dengan cheat detection  
✅ **Automated Report Generation** dengan PDF/Excel export  

**Status Sistem: ✅ PRODUCTION READY**

---
*Last Updated: July 2026 | Version: 1.0.0 | Environment: Production*
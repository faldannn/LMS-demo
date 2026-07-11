# Arsitektur Sistem LMS

## Gambaran Umum

Sistem LMS SMA Islam Assyafiiyah 02 Bekasi dibangun dengan **Laravel 11** menggunakan arsitektur **Service Layer Pattern** yang memisahkan business logic dari controllers.

## Struktur Direktori

```
app/
├── Console/           # Artisan commands (backup, pruning)
├── Enums/            # Enum definitions (UserRole, etc.)
├── Exports/          # Excel export classes
├── Http/
│   ├── Controllers/  # 30+ controllers dengan role-based routing
│   ├── Middleware/   # Security, role, active user check
│   └── Requests/     # Form request validation
├── Imports/          # Excel import classes (master data)
├── Jobs/             # Queue jobs (notifications, etc.)
├── Models/           # 40+ Eloquent models dengan relationships
├── Policies/         # Authorization policies
├── Providers/        # Service providers
├── Repositories/     # Data access layer (master data)
├── Services/         # Business logic layer
└── Support/          # Utilities dan helpers
```

## Service Layer Architecture

### 1. **Controller Layer**
- Menangani HTTP requests/responses
- Memanggil services untuk business logic
- Menggunakan Form Requests untuk validation
- Menggunakan Policies untuk authorization

### 2. **Service Layer** (Business Logic)
```
Services/
├── AcademicCalendarService.php
├── ActivityLogService.php
├── AnnouncementService.php
├── AssignmentService.php
├── AttendanceService.php
├── AuthService.php
├── CommentService.php
├── DashboardService.php
├── DiscussionService.php
├── FileStorageService.php
├── GeofenceService.php
├── MaterialService.php
├── QrAttendanceService.php
├── ReportCardService.php
├── ReportService.php
├── SelfAttendanceService.php
├── SessionManagementService.php
└── MasterData/        # Services untuk data master
```

### 3. **Repository Layer** (Data Access)
- Menggunakan repository pattern untuk master data
- Eager loading untuk optimasi query
- Soft delete untuk data retention

## Database Architecture

### Entity Relationship Diagram

```
USER (kepala_sekolah, guru, siswa)
    ├── TEACHER (jika role=guru)
    ├── STUDENT (jika role=siswa)
    └── STUDENT_PARENT (relasi orang tua)

ACADEMIC_YEAR ─┬─ CLASS ─┬─ STUDENT
               │         ├─ TEACHER (wali kelas)
               │         └─ CLASS_SUBJECT ─┬─ SUBJECT
               │                           ├─ TEACHER (pengajar)
               │                           └─ ACADEMIC_YEAR
               │
               └─ SEMESTER

CLASS_SUBJECT ─┬─ MATERIAL (pdf, ppt, video, youtube, link, image)
               ├─ ASSIGNMENT ─┬─ ASSIGNMENT_SUBMISSION ── ASSIGNMENT_GRADE
               │              └─ ASSIGNMENT_GRADE_RUBRIC
               ├─ QUIZ ─┬─ QUESTION ── QUESTION_OPTION
               │        ├─ QUIZ_ATTEMPT ── STUDENT_ANSWER
               │        └─ QUIZ_QUESTION (bank soal)
               └─ DISCUSSION_THREAD ── COMMENT

ATTENDANCE ── ATTENDANCE_DETAIL ── STUDENT
REPORT_CARD ── REPORT_CARD_DETAIL ── SUBJECT

LOGIN_LOG ── USER
ACTIVITY_LOG ── USER
```

### Key Database Features

1. **Soft Deletes**: Master data menggunakan soft delete untuk audit trail
2. **Polymorphic Relationships**: Comments system untuk multiple entities
3. **Composite Unique Constraints**: Mencegah data duplikat
4. **Foreign Key Constraints**: Data integrity
5. **Performance Indexes**: Optimasi query untuk tabel besar

## Security Architecture

### 1. **Authentication System**
- Laravel built-in authentication
- Rate limiting: 10 attempts/minute + lockout after 5 failures
- Session management dengan database driver
- Last login tracking

### 2. **Authorization System**
- **Role-Based Access Control (RBAC)**: 3 roles utama
- **Policies**: Fine-grained permission control
- **Middleware**: Role middleware untuk route protection
- **Gates**: Business logic authorization

### 3. **Security Headers**
```php
// SecurityHeaders Middleware
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
Referrer-Policy: strict-origin-when-cross-origin
Strict-Transport-Security: max-age=31536000; includeSubDomains
```

### 4. **Data Protection**
- **Encryption**: Sensitive data encryption
- **Validation**: Input validation di semua layers
- **File Upload Security**: MIME type dan size validation
- **XSS Protection**: Blade template escaping

## Performance Optimization

### 1. **Database Optimization**
- **Indexes**: Strategic indexing untuk frequent queries
- **Eager Loading**: Mencegah N+1 query problems
- **Query Caching**: Frequently accessed data caching
- **Database Queue**: Async processing untuk heavy operations

### 2. **Application Caching**
```php
// Cache strategies
- Config caching: php artisan config:cache
- Route caching: php artisan route:cache  
- View caching: php artisan view:cache
- Query caching: Cache::remember()
```

### 3. **Asset Optimization**
- Bootstrap 5 minified CSS/JS
- Laravel Mix untuk asset compilation
- CDN untuk external libraries
- Image optimization untuk uploads

## Deployment Architecture

### 1. **Development Environment**
- Local development dengan Laravel Sail/Valet
- SQLite untuk development database
- Asset compilation dengan Laravel Mix
- Debug mode dengan detailed errors

### 2. **Staging Environment**
- Mirip production environment
- Testing database dengan dummy data
- Performance testing
- User acceptance testing

### 3. **Production Environment**
- **Web Server**: Nginx dengan PHP-FPM
- **Database**: MySQL 8.0+ dengan replication
- **Queue**: Supervisor managed workers
- **Cron**: Scheduled tasks (backup, pruning)
- **Monitoring**: Health checks dan error tracking

## Integration Architecture

### 1. **Internal Integrations**
- **File Storage**: Laravel Storage (local/S3)
- **Queue System**: Database queue dengan jobs
- **Cache System**: Redis/Database cache
- **Session**: Database session storage

### 2. **External Integrations**
- **WhatsApp API**: Fonnte API untuk notifikasi
- **QR Code Generation**: Simple QR Code library
- **PDF Generation**: DomPDF untuk reporting
- **Excel Export**: Maatwebsite Excel

### 3. **API Architecture** (Future)
- RESTful API dengan authentication
- Rate limiting per user/role
- API documentation dengan OpenAPI
- Webhook support untuk integrations

## Scalability Considerations

### 1. **Vertical Scaling**
- PHP-FPM process management
- Database connection pooling
- Memory optimization
- Cache layer expansion

### 2. **Horizontal Scaling**
- Load balancer ready architecture
- Stateless application design
- Database read replicas
- File storage scaling (S3 compatible)

### 3. **Monitoring & Alerts**
- Application performance monitoring
- Database performance metrics
- Error tracking dan alerting
- User activity analytics

## Maintenance Architecture

### 1. **Backup System**
- Automated daily database backups
- File storage backups
- Backup retention policy
- Recovery procedures documented

### 2. **Update Procedures**
- Version control dengan Git
- Database migration system
- Rolling updates strategy
- Rollback procedures

### 3. **Documentation**
- Code documentation dengan PHPDoc
- Database schema documentation
- API documentation
- Operational runbooks

---

## Technology Stack Summary

| Layer | Technology | Purpose |
|-------|------------|---------|
| **Framework** | Laravel 11 | MVC framework dengan ecosystem |
| **Language** | PHP 8.3+ | Backend programming language |
| **Database** | MySQL 8.0+ | Relational database |
| **Frontend** | Bootstrap 5 | CSS framework untuk responsive design |
| **Templating** | Blade | Laravel templating engine |
| **Queue** | Database Queue | Async job processing |
| **Cache** | Redis/Database | Performance optimization |
| **File Storage** | Local/S3 | File uploads storage |
| **PDF Generation** | DomPDF | Report generation |
| **Excel** | Maatwebsite | Data export/import |
| **QR Code** | Simple QR Code | Attendance QR generation |
| **Charts** | Chart.js | Data visualization |
| **Notifications** | Fonnte API | WhatsApp integration |

---

*Architecture Document Version: 1.0.0 | Last Updated: July 2026*
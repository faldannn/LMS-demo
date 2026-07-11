# Checklist Deployment Produksi

**LMS SMA Islam Assyafiiyah 02 Bekasi**  
Gunakan dokumen ini sebelum go-live di server produksi.

---

## 1. Prasyarat Server

- [ ] PHP **8.3+** dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `gd`, `zip`, `bcmath`, `fileinfo`
- [ ] **Composer** 2.x
- [ ] **Node.js** 18+ & npm (untuk build asset)
- [ ] **MySQL** 8.0+ atau MariaDB 10.6+ (disarankan untuk produksi)
- [ ] **Nginx** atau Apache dengan HTTPS (Let's Encrypt)
- [ ] **Supervisor** atau systemd untuk queue worker & scheduler
- [ ] `mysqldump` terpasang (untuk backup MySQL)

---

## 2. Clone & Environment

```bash
git clone <repo-url> /var/www/lms-assyafiiyah
cd /var/www/lms-assyafiiyah
cp .env.example .env
php artisan key:generate
```

### `.env` produksi (wajib)

| Variabel | Nilai Produksi |
|----------|----------------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | URL HTTPS resmi sekolah |
| `DB_CONNECTION` | `mysql` |
| `DB_*` | Kredensial database produksi |
| `SESSION_DRIVER` | `database` |
| `SESSION_SECURE_COOKIE` | `true` |
| `SESSION_ENCRYPT` | `true` |
| `CACHE_STORE` | `database` atau `redis` |
| `QUEUE_CONNECTION` | `database` |
| `LOG_CHANNEL` | `daily` |
| `LOG_LEVEL` | `warning` |

---

## 3. Instalasi Aplikasi

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan db:seed --force   # hanya first deploy; hapus setelah produksi stabil
php artisan storage:link
```

---

## 4. Optimasi Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

> **Penting:** Setelah mengubah `.env` atau config, jalankan `php artisan config:clear` lalu `config:cache` lagi.

---

## 5. Permission

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

Pastikan `storage/app/backups` dapat ditulis untuk backup database.

---

## 6. Scheduler (Cron)

Tambahkan ke crontab user `www-data`:

```cron
* * * * * cd /var/www/lms-assyafiiyah && php artisan schedule:run >> /dev/null 2>&1
```

Scheduler menjalankan:
- `db:backup` — setiap hari 02:00
- `logs:prune` — mingguan (audit & login log)
- `queue:prune-failed` — mingguan

---

## 7. Queue Worker

**Supervisor** (`/etc/supervisor/conf.d/lms-worker.conf`):

```ini
[program:lms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/lms-assyafiiyah/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/lms-assyafiiyah/storage/logs/worker.log
```

```bash
supervisorctl reread && supervisorctl update && supervisorctl start lms-worker:*
```

---

## 8. Nginx (contoh)

```nginx
server {
    listen 443 ssl http2;
    server_name lms.assyafiiyah02bekasi.sch.id;
    root /var/www/lms-assyafiiyah/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 100M;
}
```

---

## 9. Keamanan — Verifikasi

- [ ] HTTPS aktif, `APP_URL` menggunakan `https://`
- [ ] `APP_DEBUG=false`
- [ ] Login rate limit aktif (10/menit + lockout 5 gagal)
- [ ] CSRF token pada semua form (`@csrf`)
- [ ] Menu **Keamanan Sistem** dapat diakses kepala sekolah
- [ ] Audit log & riwayat login berfungsi
- [ ] Backup manual dari **Keamanan → Backup** berhasil
- [ ] Upload file ditolak jika format/ukuran tidak valid
- [ ] User nonaktif tidak bisa login & sesinya dicabut

---

## 10. Backup & Recovery

### Backup manual

```bash
php artisan db:backup
```

File tersimpan di `storage/app/backups/`.

### Restore MySQL

```bash
mysql -u user -p database_name < storage/app/backups/backup_mysql_YYYY-MM-DD_HHMMSS.sql
```

### Restore SQLite

```bash
cp storage/app/backups/backup_sqlite_YYYY-MM-DD_HHMMSS.sqlite database/database.sqlite
```

---

## 11. Post-Deploy Smoke Test

- [ ] Login kepala sekolah, guru, siswa
- [ ] Dashboard memuat tanpa error
- [ ] Input absensi & presensi GPS
- [ ] Upload materi PDF
- [ ] Kuis CBT (siswa mengerjakan)
- [ ] Export laporan PDF/Excel
- [ ] Health check: `GET /up` → 200

---

## 12. Rollback

```bash
php artisan down --message="Maintenance" --retry=60
# restore backup DB
git checkout <tag-sebelumnya>
composer install --no-dev
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan up
```

---

## 13. Kontak & Dokumentasi

- Changelog sprint: `docs/SPRINT-*-CHANGELOG.md`
- Kredensial demo: hapus/ubah sebelum produksi (`DummyUserSeeder`)
- Support teknis: tim IT sekolah / vendor pengembang

---

**Status:** Sistem siap produksi setelah semua checklist di atas tercentang.

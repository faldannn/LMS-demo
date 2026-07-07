# Deployment Checklist - LMS SMA Islam Assyafiiyah 02 Bekasi

Panduan langkah demi langkah untuk melakukan deployment Sistem LMS ke server produksi (VPS Ubuntu 22.04+ atau CentOS).

---

## 1. Persiapan Server (Prerequisites)

- [ ] **OS**: Ubuntu 22.04 LTS (disarankan) atau AlmaLinux 9
- [ ] **Web Server**: Nginx
- [ ] **PHP**: Versi 8.3 dengan ekstensi: `bcmath, ctype, fileinfo, json, mbstring, openssl, pdo, tokenizer, xml, zip, gd, redis`
- [ ] **Database**: MySQL 8.0+ atau MariaDB 10.6+
- [ ] **Cache/Queue**: Redis Server
- [ ] **Proses Manager**: Supervisor (untuk worker)
- [ ] **SSL**: Let's Encrypt (Certbot)

## 2. Setup Source Code

1. Clone repository ke folder `/var/www/lms`
2. Set ownership: `sudo chown -R www-data:www-data /var/www/lms`
3. Set permission: `sudo find /var/www/lms -type f -exec chmod 644 {} \;`
4. Set permission: `sudo find /var/www/lms -type d -exec chmod 755 {} \;`
5. Pastikan folder `storage` & `bootstrap/cache` writable: `sudo chmod -R 775 /var/www/lms/storage /var/www/lms/bootstrap/cache`

## 3. Konfigurasi Environment

1. Salin `.env.production` menjadi `.env`
2. Update kredensial database (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
3. Jalankan command untuk generate application key:
   ```bash
   php artisan key:generate
   ```

## 4. Instalasi Dependensi & Build

1. Install dependensi backend (PHP):
   ```bash
   composer install --optimize-autoloader --no-dev
   ```
2. Install dependensi frontend (Node.js):
   ```bash
   npm install
   npm run build
   ```

## 5. Migrasi & Storage Link

1. Jalankan migrasi database:
   ```bash
   php artisan migrate --force
   ```
2. Buat symlink untuk storage:
   ```bash
   php artisan storage:link
   ```

## 6. Konfigurasi Nginx

Contoh konfigurasi `/etc/nginx/sites-available/lms`:
```nginx
server {
    listen 80;
    server_name lms.assyafiiyah02bekasi.sch.id;
    root /var/www/lms/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## 7. Optimasi Produksi (Wajib)

Jalankan perintah berikut untuk mencache konfigurasi dan route:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

## 8. Konfigurasi Queue Worker (Supervisor)

Buat file konfigurasi `/etc/supervisor/conf.d/lms-worker.conf`:
```ini
[program:lms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/lms/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/lms/storage/logs/worker.log
stopwaitsecs=3600
```
Update supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start lms-worker:*
```

## 9. Konfigurasi Scheduler (Cron Job)

Tambahkan ke cron tab user `www-data` (`crontab -e -u www-data`):
```bash
* * * * * cd /var/www/lms && php artisan schedule:run >> /dev/null 2>&1
```
*Catatan: Scheduler ini penting untuk pembersihan sesi aktif, cache kadaluwarsa, dan backup database harian.*

## 10. Checklist Final

- [ ] URL SSL/HTTPS dapat diakses tanpa peringatan browser
- [ ] Pengguna dapat login dan sesi tidak terputus (Session Database berfungsi)
- [ ] Upload gambar dan file berhasil (Storage Link benar)
- [ ] Kecepatan akses dashboard dalam rentang wajar (N+1 queries fixed, Cache aktif)
- [ ] Halaman `System Maintenance` > `Backup Database` berfungsi tanpa error

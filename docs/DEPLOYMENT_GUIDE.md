# Deployment Guide

## Quick Start Deployment

### 1. **Server Requirements**
- PHP 8.3+ dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `gd`, `zip`
- MySQL 8.0+ atau MariaDB 10.6+
- Composer 2.x
- Node.js 18+ & npm
- Web server (Nginx/Apache) dengan HTTPS

### 2. **Quick Deployment Script**
```bash
#!/bin/bash
# deployment.sh

# 1. Clone atau update
git pull origin main

# 2. Install dependencies
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# 3. Environment setup
cp .env.example .env
# Edit .env dengan konfigurasi produksi

# 4. Database setup
php artisan migrate --force
php artisan storage:link

# 5. Optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

## Production Configuration

### **Environment File (.env)**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://lms.assyafiiyah02bekasi.sch.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_production
DB_USERNAME=production_user
DB_PASSWORD=secure_password

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true

CACHE_DRIVER=database
QUEUE_CONNECTION=database

LOG_CHANNEL=daily
LOG_LEVEL=warning
```

## Server Setup

### **Nginx Configuration**
```nginx
server {
    listen 443 ssl http2;
    server_name lms.assyafiiyah02bekasi.sch.id;
    root /var/www/lms-assyafiiyah/public;
    
    ssl_certificate /etc/letsencrypt/live/lms.assyafiiyah02bekasi.sch.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/lms.assyafiiyah02bekasi.sch.id/privkey.pem;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    
    index index.php;
    
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

### **Supervisor Configuration**
```ini
[program:lms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/lms-assyafiiyah/artisan queue:work database --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/lms-assyafiiyah/storage/logs/worker.log
```

### **Cron Job**
```cron
* * * * * cd /var/www/lms-assyafiiyah && php artisan schedule:run >> /dev/null 2>&1
```

## Monitoring & Maintenance

### **Health Check**
```bash
# Check application status
curl -I https://lms.assyafiiyah02bekasi.sch.id/up

# Check queue workers
supervisorctl status lms-worker

# Check cron jobs
crontab -l
```

### **Backup Commands**
```bash
# Manual backup
php artisan db:backup

# List backups
ls -la storage/app/backups/

# Restore backup
mysql -u user -p database < storage/app/backups/backup_YYYY-MM-DD.sql
```

### **Maintenance Commands**
```bash
# Put application in maintenance mode
php artisan down --message="Maintenance" --retry=60

# Bring application back online
php artisan up

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize
php artisan optimize
```

## Security Checklist

### **Pre-Deployment**
- [ ] HTTPS configured dengan SSL certificate
- [ ] `.env` file secured (permissions 640)
- [ ] Database credentials updated
- [ ] Default demo accounts removed/changed
- [ ] File upload limits configured

### **Post-Deployment**
- [ ] Login functionality working
- [ ] File uploads working
- [ ] Email/WhatsApp notifications working
- [ ] Backup system functional
- [ ] Error pages displaying correctly
- [ ] All user roles can access their dashboards

## Troubleshooting

### **Common Issues**

#### 1. **File Upload Errors**
```bash
# Check storage permissions
chmod -R 775 storage
chown -R www-data:www-data storage

# Check PHP upload limits
php -i | grep upload_max_filesize
```

#### 2. **Database Connection Issues**
```bash
# Test database connection
php artisan tinker
DB::connection()->getPdo();

# Check migrations
php artisan migrate:status
```

#### 3. **Queue Worker Issues**
```bash
# Restart supervisor
supervisorctl restart lms-worker:*

# Check failed jobs
php artisan queue:failed
php artisan queue:retry all
```

#### 4. **Cache Issues**
```bash
# Clear all caches
php artisan optimize:clear

# Re-cache everything
php artisan optimize
```

## Rollback Procedure

### **Emergency Rollback**
```bash
# 1. Enable maintenance mode
php artisan down --message="Emergency maintenance" --retry=300

# 2. Restore from backup
mysql -u user -p lms_production < /path/to/backup.sql

# 3. Git rollback (if needed)
git checkout <previous-tag>

# 4. Reinstall dependencies
composer install --no-dev

# 5. Run migrations
php artisan migrate --force

# 6. Clear cache
php artisan optimize:clear && php artisan optimize

# 7. Disable maintenance mode
php artisan up
```

## Performance Optimization

### **Database Optimization**
```sql
-- Add indexes for frequent queries
CREATE INDEX idx_attendance_date ON attendance(date);
CREATE INDEX idx_materials_published ON materials(is_published);
CREATE INDEX idx_assignments_due_date ON assignments(due_date);
```

### **PHP-FPM Optimization**
```ini
; /etc/php/8.3/fpm/pool.d/www.conf
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500
```

### **Nginx Optimization**
```nginx
# Enable gzip compression
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;
```

## Scaling Considerations

### **Vertical Scaling**
- Increase PHP-FPM `pm.max_children`
- Increase MySQL `innodb_buffer_pool_size`
- Add more RAM to server

### **Horizontal Scaling**
1. Setup load balancer
2. Use Redis for centralized cache
3. Use S3 for file storage
4. Database read replicas

## Support & Contact

### **Emergency Contacts**
- **Technical Support**: IT Department - 021-XXXXXXX
- **System Administrator**: [admin@assyafiiyah02bekasi.sch.id](mailto:admin@assyafiiyah02bekasi.sch.id)
- **Developer**: NexDigi System - [support@nexdigi.id](mailto:support@nexdigi.id)

### **Monitoring Tools**
- **Application**: Laravel Logs (`storage/logs/`)
- **Server**: `htop`, `nginx status`
- **Database**: `SHOW PROCESSLIST`, slow query log
- **Network**: `netstat`, `iftop`

---

## Quick Reference

### **Essential Commands**
```bash
# Deployment
./deploy.sh

# Maintenance
php artisan down && php artisan up

# Backup
php artisan db:backup

# Monitoring
php artisan queue:work --once
tail -f storage/logs/laravel.log
```

### **File Locations**
- **Logs**: `/var/www/lms-assyafiiyah/storage/logs/`
- **Backups**: `/var/www/lms-assyafiiyah/storage/app/backups/`
- **Environment**: `/var/www/lms-assyafiiyah/.env`
- **Config**: `/var/www/lms-assyafiiyah/config/`

### **Ports & Services**
- **HTTP/HTTPS**: 80/443
- **MySQL**: 3306
- **SSH**: 22
- **PHP-FPM**: Unix socket

---

*Deployment Guide Version: 1.0.0 | Last Updated: July 2026*
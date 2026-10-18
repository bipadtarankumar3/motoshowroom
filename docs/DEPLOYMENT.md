# MotoShowroom SaaS - Production Deployment Guide (Ubuntu VPS)

## 1. Server Prerequisites
* **Operating System**: Ubuntu 24.04 LTS or 22.04 LTS
* **Web Server**: Nginx
* **PHP Engine**: PHP 8.2+ or PHP 8.5 with FPM, extensions: `bcmath, curl, gd, intl, mbstring, pdo_mysql, zip, redis`
* **Database**: MySQL 8.0 Server
* **Process Manager**: Supervisor for background queues (`queue:work`)
* **Cron Service**: System crontab for Laravel Scheduler (`schedule:run`)
* **SSL / TLS**: Let's Encrypt Certbot for wildcard/custom domains

---

## 2. Nginx Configuration
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name motoshowroom.com *.motoshowroom.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name motoshowroom.com *.motoshowroom.com;
    root /var/www/motoshowroom/public;

    ssl_certificate /etc/letsencrypt/live/motoshowroom.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/motoshowroom.com/privkey.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.5-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 3. Supervisor Queue Worker Configuration
`/etc/supervisor/conf.d/motoshowroom-worker.conf`:
```ini
[program:motoshowroom-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/motoshowroom/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/motoshowroom/storage/logs/worker.log
stopwaitsecs=3600
```

---

## 4. Cron Task for Scheduler
```bash
* * * * * cd /var/www/motoshowroom && php artisan schedule:run >> /dev/null 2>&1
```

---

## 5. Deployment Script
```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm ci
npm run build
php artisan queue:restart
sudo supervisorctl restart motoshowroom-worker:*
```

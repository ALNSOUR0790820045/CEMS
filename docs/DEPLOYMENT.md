# Deployment Guide

## Server Requirements

- Ubuntu 22.04 LTS
- PHP 8.2+
- MySQL 8.0+
- Nginx 1.18+
- Redis 6.0+
- Supervisor
- SSL Certificate

## Production Setup

### 1. Server Preparation

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml \
  php8.2-curl php8.2-zip php8.2-gd php8.2-redis php8.2-bcmath php8.2-intl

# Install MySQL
sudo apt install -y mysql-server

# Secure MySQL installation
sudo mysql_secure_installation

# Install Nginx
sudo apt install -y nginx

# Install Redis
sudo apt install -y redis-server

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js 18
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Supervisor
sudo apt install -y supervisor
```

### 2. Database Setup

```bash
# Login to MySQL
sudo mysql

# Create database and user
CREATE DATABASE cems_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'cems_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON cems_db.* TO 'cems_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Application Deployment

```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/ALNSOUR0790820045/CEMS.git
cd CEMS

# Set permissions
sudo chown -R www-data:www-data /var/www/CEMS
sudo chmod -R 755 /var/www/CEMS/storage
sudo chmod -R 755 /var/www/CEMS/bootstrap/cache

# Install dependencies
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm install
sudo -u www-data npm run build

# Configure environment
sudo -u www-data cp .env.example .env
sudo -u www-data php artisan key:generate

# Edit .env with production values
sudo nano .env
```

### 4. Environment Configuration

Update `.env` file with production settings:

```env
APP_NAME=CEMS
APP_ENV=production
APP_KEY=base64:generated_key_here
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=https://your-domain.com

LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cems_db
DB_USERNAME=cems_user
DB_PASSWORD=your_strong_password

BROADCAST_CONNECTION=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis

CACHE_STORE=redis
CACHE_PREFIX=cems_cache

SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-mail-host
MAIL_PORT=587
MAIL_USERNAME=your-mail-username
MAIL_PASSWORD=your-mail-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Run Migrations

```bash
# Run migrations
sudo -u www-data php artisan migrate --force

# Run seeders (optional for initial data)
sudo -u www-data php artisan db:seed --force

# Cache configuration
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

### 6. Nginx Configuration

Create Nginx configuration file:

```bash
sudo nano /etc/nginx/sites-available/cems
```

Add the following configuration:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/CEMS/public;
    
    add_header X-Frame-Options "SAMEORIGIN";
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
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/javascript application/json;
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/cems /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 7. SSL Setup with Let's Encrypt

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtain SSL certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Test auto-renewal
sudo certbot renew --dry-run
```

### 8. Queue Worker Setup

Create Supervisor configuration:

```bash
sudo nano /etc/supervisor/conf.d/cems-worker.conf
```

Add the following:

```ini
[program:cems-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/CEMS/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/CEMS/storage/logs/worker.log
stopwaitsecs=3600
```

Start the queue worker:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start cems-worker:*
```

### 9. Scheduled Tasks

Add Laravel scheduler to crontab:

```bash
sudo crontab -e -u www-data
```

Add the following line:

```
* * * * * cd /var/www/CEMS && php artisan schedule:run >> /dev/null 2>&1
```

### 10. Log Rotation

Create log rotation configuration:

```bash
sudo nano /etc/logrotate.d/cems
```

Add:

```
/var/www/CEMS/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

## Zero-Downtime Deployment

### Deployment Script

Create a deployment script at `/var/www/deploy-cems.sh`:

```bash
#!/bin/bash

set -e

echo "🚀 Starting deployment..."

# Navigate to project directory
cd /var/www/CEMS

# Enable maintenance mode
php artisan down --message="Upgrading system. Please wait..." --retry=60

# Pull latest changes
git pull origin main

# Install/update dependencies
composer install --no-dev --optimize-autoloader --no-interaction
npm install
npm run build

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations
php artisan migrate --force

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers
sudo supervisorctl restart cems-worker:*

# Disable maintenance mode
php artisan up

echo "✅ Deployment completed successfully!"
```

Make it executable:

```bash
chmod +x /var/www/deploy-cems.sh
```

## Monitoring

### Application Monitoring

Install Laravel Telescope for debugging (development only):

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### Server Monitoring

1. **Monitor disk space:**
```bash
df -h
```

2. **Monitor memory:**
```bash
free -m
```

3. **Monitor processes:**
```bash
top
htop
```

4. **Check logs:**
```bash
tail -f /var/www/CEMS/storage/logs/laravel.log
tail -f /var/log/nginx/error.log
```

### Health Checks

Create a simple health check endpoint at `/health` that returns:
- Application status
- Database connection
- Redis connection
- Queue status

## Backup Strategy

### Database Backup

Create backup script at `/var/www/backup-cems.sh`:

```bash
#!/bin/bash

BACKUP_DIR="/var/backups/cems"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="cems_db"
DB_USER="cems_user"
DB_PASS="your_strong_password"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_backup_$DATE.sql.gz

# Backup application files
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz /var/www/CEMS/storage/app

# Remove backups older than 30 days
find $BACKUP_DIR -type f -mtime +30 -delete

echo "Backup completed: $DATE"
```

Schedule daily backups:

```bash
sudo crontab -e
```

Add:

```
0 2 * * * /var/www/backup-cems.sh >> /var/log/cems-backup.log 2>&1
```

### Using Laravel Backup Package

The system uses `spatie/laravel-backup`. Configure it in `config/backup.php` and run:

```bash
# Manual backup
php artisan backup:run

# Schedule automatic backups (already in scheduler)
```

## Security Hardening

### 1. Firewall Setup

```bash
# Enable UFW
sudo ufw enable

# Allow SSH
sudo ufw allow 22/tcp

# Allow HTTP and HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Check status
sudo ufw status
```

### 2. Secure PHP Configuration

Edit `/etc/php/8.2/fpm/php.ini`:

```ini
expose_php = Off
display_errors = Off
log_errors = On
max_execution_time = 60
memory_limit = 256M
post_max_size = 50M
upload_max_filesize = 50M
```

Restart PHP-FPM:

```bash
sudo systemctl restart php8.2-fpm
```

### 3. Secure MySQL

Edit `/etc/mysql/mysql.conf.d/mysqld.cnf`:

```ini
bind-address = 127.0.0.1
skip-name-resolve
```

Restart MySQL:

```bash
sudo systemctl restart mysql
```

### 4. File Permissions

```bash
# Set proper ownership
sudo chown -R www-data:www-data /var/www/CEMS

# Set directory permissions
sudo find /var/www/CEMS -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/CEMS -type f -exec chmod 644 {} \;

# Make storage and cache writable
sudo chmod -R 775 /var/www/CEMS/storage
sudo chmod -R 775 /var/www/CEMS/bootstrap/cache
```

## Troubleshooting

### Common Issues

1. **500 Internal Server Error**
   - Check Laravel logs: `/var/www/CEMS/storage/logs/laravel.log`
   - Check Nginx logs: `/var/log/nginx/error.log`
   - Verify file permissions

2. **Queue not processing**
   - Check supervisor status: `sudo supervisorctl status`
   - Restart workers: `sudo supervisorctl restart cems-worker:*`
   - Check logs: `/var/www/CEMS/storage/logs/worker.log`

3. **Database connection failed**
   - Verify credentials in `.env`
   - Check MySQL is running: `sudo systemctl status mysql`
   - Test connection: `mysql -u cems_user -p cems_db`

4. **Permission denied errors**
   - Fix storage permissions: `sudo chmod -R 775 storage bootstrap/cache`
   - Fix ownership: `sudo chown -R www-data:www-data /var/www/CEMS`

## Performance Optimization

### 1. OPcache Configuration

Edit `/etc/php/8.2/fpm/php.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### 2. Redis Configuration

Edit `/etc/redis/redis.conf`:

```
maxmemory 256mb
maxmemory-policy allkeys-lru
```

### 3. MySQL Optimization

Edit `/etc/mysql/mysql.conf.d/mysqld.cnf`:

```ini
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
query_cache_size = 0
query_cache_type = 0
```

## Scaling

### Horizontal Scaling

1. Set up load balancer (Nginx/HAProxy)
2. Configure shared storage (NFS/S3)
3. Set up central Redis server
4. Configure database replication
5. Use queue workers on separate servers

### Vertical Scaling

1. Increase server resources (CPU, RAM, Disk)
2. Optimize database queries
3. Implement caching strategies
4. Use CDN for static assets

## Maintenance

### Regular Tasks

1. **Daily:**
   - Monitor error logs
   - Check disk space
   - Verify backups

2. **Weekly:**
   - Review performance metrics
   - Check security updates
   - Clean old logs

3. **Monthly:**
   - Update dependencies
   - Review and optimize database
   - Test disaster recovery

---

For additional support, contact the development team or refer to the [main documentation](../README.md).

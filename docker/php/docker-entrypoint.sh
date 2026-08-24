#!/bin/sh
set -e

cd /var/www/html

mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    storage/app/public/qrcode \
    storage/app/public/profiles/logos \
    storage/app/public/profiles/pictures \
    storage/app/public/profiles/documents

# Local Docker: allow php-fpm (www-data) to write on named volumes.
# Clear root-owned compiled views so Blade can touch() timestamps.
mkdir -p /tmp/laravel-views
if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data storage bootstrap/cache /tmp/laravel-views 2>/dev/null || true
fi
chmod -R 777 storage bootstrap/cache storage/app/public /tmp/laravel-views 2>/dev/null || true
find storage/framework/views -type f -name '*.php' -delete 2>/dev/null || true
find /tmp/laravel-views -type f -name '*.php' -delete 2>/dev/null || true

# Serve uploaded QR codes, logos, etc. at /storage/*
php artisan storage:link --force >/dev/null 2>&1 || true

# Scheduler role: run Laravel's scheduler (schedule:work runs due tasks every minute) so
# dailyAt() jobs actually fire — code-expiry emails (30/3/0 days), VOC document expiry,
# participant reminders. The storage/cache-path setup above already ran, so Blade email
# views compile cleanly here too.
if [ "$CONTAINER_ROLE" = "scheduler" ]; then
    exec php artisan schedule:work
fi

exec docker-php-entrypoint php-fpm

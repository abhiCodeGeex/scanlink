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
if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
fi
chmod -R 777 storage bootstrap/cache storage/app/public 2>/dev/null || true
find storage/framework/views -type f -name '*.php' -delete 2>/dev/null || true

# Serve uploaded QR codes, logos, etc. at /storage/*
php artisan storage:link --force >/dev/null 2>&1 || true

exec docker-php-entrypoint php-fpm

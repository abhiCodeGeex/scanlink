#!/bin/sh
set -e

cd /var/www/html

mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Local Docker: allow php-fpm (www-data) to write on named volumes.
chmod -R 777 storage bootstrap/cache 2>/dev/null || true

exec docker-php-entrypoint php-fpm
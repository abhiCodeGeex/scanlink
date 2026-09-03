FROM php:8.4-fpm-alpine

RUN set -eux; \
    apk update; \
    apk add --no-cache \
        bash \
        curl \
        git \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
        unzip \
        zip \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        mariadb-client \
        $PHPIZE_DEPS; \
    pecl install redis; \
    docker-php-ext-enable redis; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j"$(nproc)" \
        bcmath \
        gd \
        intl \
        pdo_mysql \
        opcache \
        zip; \
    apk del --no-network $PHPIZE_DEPS; \
    rm -rf /tmp/pear /var/cache/apk/*

COPY docker/php/conf.d/zz-performance.ini /usr/local/etc/php/conf.d/zz-performance.ini
COPY docker/php/docker-entrypoint.sh /usr/local/bin/scanlink-entrypoint.sh

RUN chmod +x /usr/local/bin/scanlink-entrypoint.sh

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

ENTRYPOINT ["/usr/local/bin/scanlink-entrypoint.sh"]

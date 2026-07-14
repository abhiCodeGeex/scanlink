FROM php:8.4.1-fpm-alpine

RUN apk add --no-cache \
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
    $PHPIZE_DEPS \
    && git clone --depth 1 --branch 6.2.0 https://github.com/phpredis/phpredis.git /tmp/phpredis \
    && cd /tmp/phpredis \
    && phpize \
    && ./configure \
    && make -j$(nproc) \
    && make install \
    && docker-php-ext-enable redis \
    && cd / \
    && rm -rf /tmp/phpredis \
    && apk del $PHPIZE_DEPS

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        gd \
        intl \
        pdo_mysql \
        opcache \
        zip

COPY docker/php/conf.d/zz-performance.ini /usr/local/etc/php/conf.d/zz-performance.ini
COPY docker/php/docker-entrypoint.sh /usr/local/bin/scanlink-entrypoint.sh

RUN chmod +x /usr/local/bin/scanlink-entrypoint.sh

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

ENTRYPOINT ["/usr/local/bin/scanlink-entrypoint.sh"]

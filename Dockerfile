FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# Instal dependensi sistem
RUN apk add --no-cache \
    bash \
    sed \
    icu-dev \
    libzip-dev \
    libpng-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    oniguruma-dev \
    gettext-dev \
    mysql-client \
    git \
    curl \
    unzip

# Instal & konfigurasi ekstensi PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    gettext

# Instal Composer secara langsung dari image resmi (lebih cepat & aman)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Memastikan folder storage dan cache ada dan memiliki permission yang benar
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

USER www-data
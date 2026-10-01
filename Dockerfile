# ==========================================
# Giai đoạn 1: Build dependencies với Composer
# ==========================================
FROM composer:2.7 AS builder

WORKDIR /var/www/html

# Sao chép file định nghĩa package để tận dụng Docker cache
COPY composer.json composer.lock ./

# Cài đặt vendor không bao gồm dev dependencies
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-autoloader \
    --no-scripts

# Sao chép toàn bộ mã nguồn vào image builder
COPY . .

# Sinh autoload classmap tối ưu cho production
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

# ==========================================
# Giai đoạn 2: Production image với PHP 8.2-FPM & Nginx
# ==========================================
FROM php:8.2-fpm-alpine AS production

# Cài đặt Nginx, Tini, Bash, Gettext (envsubst) và các thư viện cần thiết cho extensions
RUN apk add --no-cache \
    nginx \
    tini \
    bash \
    gettext \
    libpng \
    libpng-dev \
    libjpeg-turbo \
    libjpeg-turbo-dev \
    freetype \
    freetype-dev \
    libzip \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql gd zip opcache \
    && apk del --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    && rm -rf /var/cache/apk/*

# Cấu hình OPcache tối ưu
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Cấu hình tài nguyên PHP
RUN { \
    echo 'upload_max_filesize=32M'; \
    echo 'post_max_size=32M'; \
    echo 'memory_limit=256M'; \
} > /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html

# Sao chép mã nguồn và vendor từ builder
COPY --from=builder /var/www/html /var/www/html

# Sao chép cấu hình Nginx và entrypoint script
COPY docker/nginx.conf /etc/nginx/nginx.conf.template
COPY docker/entrypoint.sh /entrypoint.sh

# Cấp quyền thực thi cho entrypoint và quyền ghi cho storage, bootstrap/cache
RUN chmod +x /entrypoint.sh \
    && mkdir -p /var/www/html/storage/framework/cache/data \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Cổng mặc định
EXPOSE 80

# Chạy entrypoint qua Tini init process
ENTRYPOINT ["/sbin/tini", "--", "/entrypoint.sh"]

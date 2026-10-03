# ---------- Stage 1: build front-end assets (Vite) ----------
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json* ./
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi
COPY . .
RUN npm run build

# ---------- Stage 2: PHP + nginx ----------
FROM php:8.4-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx supervisor git unzip zip curl gettext-base \
    libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libwebp-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install -j"$(nproc)" gd zip pdo_mysql opcache \
 && rm -rf /var/lib/apt/lists/*

RUN { \
      echo "upload_max_filesize=10M"; \
      echo "post_max_size=12M"; \
      echo "memory_limit=256M"; \
      echo "max_execution_time=60"; \
      echo "expose_php=Off"; \
      echo "display_errors=Off"; \
      echo "log_errors=On"; \
      echo "opcache.enable=1"; \
      echo "opcache.memory_consumption=128"; \
      echo "opcache.max_accelerated_files=10000"; \
      echo "opcache.validate_timestamps=0"; \
    } > /usr/local/etc/php/conf.d/zz-app.ini

# clear_env=no: PHP-FPM workers keep Railway's environment variables
RUN { \
      echo "[www]"; \
      echo "listen = 127.0.0.1:9000"; \
      echo "clear_env = no"; \
      echo "catch_workers_output = yes"; \
      echo "decorate_workers_output = no"; \
    } > /usr/local/etc/php-fpm.d/zz-railway.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction --no-progress

COPY . .
COPY --from=assets /app/public/build ./public/build
RUN composer dump-autoload --optimize --no-dev

RUN mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY deploy/nginx.conf /etc/nginx/sites-available/default
COPY deploy/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY deploy/entrypoint.sh /entrypoint.sh

# Strip Windows line endings (CRLF breaks #!/bin/sh scripts)
RUN sed -i 's/\r$//' /entrypoint.sh /etc/nginx/sites-available/default /etc/supervisor/conf.d/supervisord.conf \
    && chmod +x /entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]

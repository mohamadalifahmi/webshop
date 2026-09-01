# syntax=docker/dockerfile:1

# ---- Composer stage: install PHP deps ----
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --ignore-platform-req=ext-pcntl --ignore-platform-req=ext-exif --no-interaction && composer dump-autoload --optimize --no-interaction

# ---- Node stage: build frontend assets ----
FROM node:24 AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY . .
RUN npm run build

# ---- Runtime: FrankenPHP ----
FROM dunglas/frankenphp:php8.4-alpine

RUN install-php-extensions pdo_mysql pcntl exif redis ctype curl dom fileinfo mbstring openssl opcache

WORKDIR /app

COPY . .

COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

RUN chmod -R 777 storage bootstrap/cache

# healthcheck friendly - expose 8080 default (Laravel artisan serve default)
EXPOSE 8080

CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]

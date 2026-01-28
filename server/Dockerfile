FROM php:8.4-cli-alpine

WORKDIR /app

RUN apk add --no-cache \
    postgresql-dev \
    unzip \
 && docker-php-ext-install pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .
RUN composer install \
    --no-interaction \
    --no-scripts

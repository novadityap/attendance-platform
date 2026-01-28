FROM php:8.4-cli-alpine

WORKDIR /app

RUN apk add --no-cache \
    postgresql-dev \
    unzip \
 && docker-php-ext-install pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

RUN composer install \
    --prefer-dist \
    --no-interaction \
    --no-progress \

COPY . .

RUN composer dump-autoload --optimize --no-scripts


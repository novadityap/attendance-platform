FROM php:8.4-cli-alpine

WORKDIR /app

RUN apk add --no-cache zip unzip git && \
    chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pdo_pgsql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-scripts --prefer-dist --no-autoloader

COPY . .

RUN composer dump-autoload --optimize && \
    chown -R www-data:www-data storage bootstrap/cache
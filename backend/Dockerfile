FROM php:8.4.27-fpm-alpine

RUN apk add --no-cache \
        git \
        unzip \
        libzip-dev \
        icu-dev \
    && docker-php-ext-install pdo pdo_mysql opcache intl \
    && apk del git unzip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --no-dev \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

CMD ["php-fpm"]

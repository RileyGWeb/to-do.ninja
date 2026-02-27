FROM php:8.2-cli-alpine

RUN apk add --no-cache git curl unzip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

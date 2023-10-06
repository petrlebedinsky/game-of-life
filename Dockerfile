FROM php:8.1-fpm

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apk update && \
    apk upgrade && \
    apk add bash git libxml2-dev

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS && \
    pecl install xdebug-3.1.5 && \
    docker-php-ext-enable xdebug

RUN docker-php-ext-install dom zip &&  \
    docker-php-ext-enable dom zip

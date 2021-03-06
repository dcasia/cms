FROM php:7.4-fpm-alpine

RUN apk add --no-cache curl git libzip-dev libpng-dev

RUN docker-php-ext-install -j$(nproc) gd

RUN curl -sS https://install.phpcomposer.com/installer | php && \
    mv composer.phar /usr/local/bin/composer

RUN addgroup --gid 1000 composer && \
    adduser --disabled-password --ingroup composer --uid 1000 composer

USER composer

CMD ["php-fpm"]

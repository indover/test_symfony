FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/symfony

COPY ../.. .

RUN composer install

RUN chown -R www-data:www-data /var/www/symfony/var

EXPOSE 9000

CMD ["php-fpm"]
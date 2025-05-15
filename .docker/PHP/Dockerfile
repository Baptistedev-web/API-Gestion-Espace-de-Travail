FROM php:8.3-fpm

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

# Installe les dépendances utiles (composer, etc.)
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libicu-dev libxml2-dev libpng-dev libjpeg-dev libfreetype6-dev \
    zlib1g-dev libonig-dev curl openssl && \
    docker-php-ext-install pdo pdo_mysql zip intl xml gd

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installation de Xdebug pour la couverture de code
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Configuration Xdebug
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini


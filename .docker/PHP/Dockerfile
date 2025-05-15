FROM php:8.3-fpm

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

# Installe les dépendances utiles (composer, etc.)
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libicu-dev libxml2-dev libpng-dev libjpeg-dev libfreetype6-dev \
    zlib1g-dev libonig-dev curl openssl && \
    docker-php-ext-install pdo pdo_mysql zip intl xml gd

# Installe Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copie le code source (optionnel, sinon bind-mount)
# COPY . /var/www/html

# ...autres instructions si besoin...

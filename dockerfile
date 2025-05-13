# Étape de build
FROM php:8.3-fpm-alpine AS symfony_php

# Installer les dépendances système
RUN apk add --no-cache \
    acl \
    fcgi \
    file \
    gettext \
    git \
    mysql-client \
    nginx \
    supervisor \
    zip

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Installer et configurer OPcache
RUN docker-php-ext-install opcache
# Création directe du fichier opcache.ini au lieu de le copier
RUN echo '[opcache]\n\
    opcache.enable=1\n\
    opcache.revalidate_freq=0\n\
    opcache.validate_timestamps=0\n\
    opcache.max_accelerated_files=10000\n\
    opcache.memory_consumption=128\n\
    opcache.max_wasted_percentage=10\n\
    opcache.interned_strings_buffer=16\n\
    opcache.fast_shutdown=1' > /usr/local/etc/php/conf.d/opcache.ini

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurer le document root
WORKDIR /var/www/project

# Copier uniquement les fichiers nécessaires pour l'installation des dépendances
COPY composer.json composer.lock symfony.lock ./
RUN set -eux; \
    mkdir -p var/cache var/log; \
    composer install --prefer-dist --no-dev --no-scripts --no-progress --no-interaction; \
    composer clear-cache

# Copier le reste des fichiers
COPY . .

RUN composer dump-autoload --optimize --classmap-authoritative

# Configurer les permissions
RUN chown -R www-data:www-data /var/www/project/var

# Exposer le port 9000 pour PHP-FPM
EXPOSE 9000

# Lancer PHP-FPM
CMD ["php-fpm"]
FROM php:8.3-fpm-alpine

# Installation des dépendances système
RUN apk add --no-cache \
    acl \
    fcgi \
    file \
    gettext \
    git \
    nginx \
    mysql-client \
    zip

# Installation des extensions PHP
RUN docker-php-ext-install pdo pdo_mysql opcache

# Configuration OPcache
RUN echo 'opcache.enable=1' > /usr/local/etc/php/conf.d/opcache.ini && \
    echo 'opcache.revalidate_freq=0' >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo 'opcache.validate_timestamps=0' >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo 'opcache.max_accelerated_files=10000' >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo 'opcache.memory_consumption=128' >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo 'opcache.max_wasted_percentage=10' >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo 'opcache.interned_strings_buffer=16' >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo 'opcache.fast_shutdown=1' >> /usr/local/etc/php/conf.d/opcache.ini

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration du répertoire de travail
WORKDIR /var/www/project

# Copie des fichiers de configuration Composer
COPY composer.json composer.lock symfony.lock ./
RUN set -eux; \
    mkdir -p var/cache var/log; \
    composer install --prefer-dist --no-dev --no-scripts --no-progress --no-interaction; \
    composer clear-cache

# Copie du reste des fichiers du projet
COPY . .

# Optimisation autoload et scripts composer
RUN composer dump-autoload --optimize --classmap-authoritative

# Configuration des permissions
RUN chown -R www-data:www-data /var/www/project/var

# Copie de la configuration Nginx
COPY nginx.conf /etc/nginx/http.d/default.conf

# Script de démarrage
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Exposition du port (Render utilisera sa propre variable PORT)
EXPOSE 80

# Commande de démarrage
CMD ["/start.sh"]
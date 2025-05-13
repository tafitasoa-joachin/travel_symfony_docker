# Étape de build
FROM php:8.3-fpm-alpine

# Installer les dépendances système
RUN apk add --no-cache \
    acl \
    fcgi \
    file \
    gettext \
    git \
    mysql-client \
    zip

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Installer et configurer OPcache - Correction de la syntaxe
RUN docker-php-ext-install opcache
RUN echo 'opcache.enable=1' > /usr/local/etc/php/conf.d/opcache.ini && \
    echo 'opcache.revalidate_freq=0' >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo 'opcache.validate_timestamps=0' >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo 'opcache.max_accelerated_files=10000' >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo 'opcache.memory_consumption=128' >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo 'opcache.max_wasted_percentage=10' >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo 'opcache.interned_strings_buffer=16' >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo 'opcache.fast_shutdown=1' >> /usr/local/etc/php/conf.d/opcache.ini

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

# Exécuter les scripts composer
RUN composer dump-autoload --optimize --classmap-authoritative

# Configurer les permissions
RUN chown -R www-data:www-data /var/www/project/var

# Corriger le problème du fichier autoload manquant
RUN if [ -f /var/www/project/vendor/autoload.php ]; then \
    cp /var/www/project/vendor/autoload.php /var/www/project/vendor/autoload_runtime.php; \
    elif [ -f /var/www/project/vendor/symfony/runtime/autoload_runtime.php ]; then \
    mkdir -p $(dirname /var/www/project/vendor/autoload_runtime.php); \
    cp /var/www/project/vendor/symfony/runtime/autoload_runtime.php /var/www/project/vendor/autoload_runtime.php; \
    else \
    echo "ERROR: Cannot find autoload file"; \
    exit 1; \
    fi

# Exposer le port (pour la documentation, Render utilise la variable $PORT)
EXPOSE 80

# Utiliser directement la commande de démarrage au lieu d'un script
CMD sh -c "php -S 0.0.0.0:${PORT:-80} -t public"
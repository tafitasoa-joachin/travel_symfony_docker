FROM php:8.2-apache

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev

# Configuration et installation des extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    intl \
    zip \
    gd

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure Apache DocumentRoot to point to the public directory
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Activation des modules Apache
RUN a2enmod rewrite

# Définition du répertoire de travail
WORKDIR /var/www/html

# Copie des fichiers composer pour un meilleur cache des layers
COPY composer.json composer.lock ./

# Installation des dépendances 
RUN composer install --no-interaction --optimize-autoloader

# Copie du reste de l'application
COPY . .

# Exécution des scripts Composer après la copie du code
RUN composer dump-autoload --optimize && \
    composer run-script post-install-cmd --no-interaction

# Création des répertoires requis et configuration des permissions
RUN mkdir -p var/cache var/log var/sessions \
    && chmod -R 777 var \
    && chown -R www-data:www-data var

# Copie de la configuration Apache
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Set appropriate permissions
RUN chown -R www-data:www-data /var/www/html/var

# Exposition du port 80
EXPOSE 80

# Copie du script d'entrée
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Définition du script d'entrée
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
#!/bin/bash
set -e

# Vérification de l'environnement
echo "Configuration de l'environnement Symfony..."

# Installation des dépendances si nécessaire
if [ ! -f vendor/autoload.php ] || [ ! -d vendor/symfony/runtime ]; then
    echo "Installation des dépendances Symfony..."
    composer install
    
    # S'assurer que le runtime Symfony est installé
    if [ ! -d vendor/symfony/runtime ]; then
        echo "Installation spécifique du composant symfony/runtime..."
        composer require symfony/runtime
    fi
fi

# Configuration de l'environnement
if [ ! -f .env.local ]; then
    echo "Création de .env.local..."
    cp .env .env.local
    echo "DATABASE_URL=mysql://ue8t5vjaz1rhvrkj:sMaDfFPkUjKaO4RdAndk@bztk5ekzudeux7v5tznc-mysql.services.clever-cloud.com:3306/bztk5ekzudeux7v5tznc" >> .env.local
fi

# Nettoyage et préparation du cache (seulement en prod)
if [ "${APP_ENV:-dev}" = "prod" ]; then
    echo "Préparation du cache pour l'environnement de production..."
    php bin/console cache:clear --env=prod --no-debug || true
    php bin/console cache:warmup --env=prod --no-debug || true
fi

# Configuration des permissions
echo "Configuration des permissions..."
chmod -R 777 var
chown -R www-data:www-data var

# Démarrage d'Apache
echo "Démarrage d'Apache..."
exec apache2-foreground
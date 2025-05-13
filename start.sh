#!/bin/sh

# Remplacer le port dans la configuration Nginx
if [ ! -z "$PORT" ]; then
  sed -i "s/listen 80/listen $PORT/g" /etc/nginx/http.d/default.conf
fi

# Démarrer PHP-FPM en arrière-plan
php-fpm -D

# Démarrer Nginx en premier plan
nginx -g "daemon off;"
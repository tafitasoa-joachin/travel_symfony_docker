#!/bin/sh

# Remplacer le port dans la configuration Nginx
if [ ! -z "$PORT" ]; then
  sed -i "s/listen 80/listen $PORT/g" /etc/nginx/http.d/default.conf
fi

# S'assurer que fastcgi_pass utilise localhost
sed -i "s/fastcgi_pass php:9000/fastcgi_pass 127.0.0.1:9000/g" /etc/nginx/http.d/default.conf

# Démarrer PHP-FPM en arrière-plan
php-fpm -D

# Vérifier la configuration Nginx
nginx -t

# Démarrer Nginx en premier plan
nginx -g "daemon off;"
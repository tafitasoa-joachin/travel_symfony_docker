#!/bin/bash

# Rechercher les références au service "database" dans tous les fichiers
echo "=== Recherche des références au service 'database' ==="
grep -r "database:" --include="*.yml" --include="*.yaml" .

echo -e "\n=== Liste des fichiers docker-compose existants ==="
find . -name "*docker-compose*.yml" -o -name "*docker-compose*.yaml"

echo -e "\n=== Vérifier le contenu du docker-compose.yml actuel ==="
cat docker-compose.yml
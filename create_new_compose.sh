#!/bin/bash

# Ce script recherche et corrige toutes les références au service "database"
# dans tous les fichiers docker-compose du projet

echo "Recherche de toutes les références au service 'database'..."

# Rechercher tous les fichiers docker-compose
compose_files=$(find . -name "docker-compose*.yml" -type f)

if [ -z "$compose_files" ]; then
    echo "Aucun fichier docker-compose trouvé!"
    exit 1
fi

echo "Fichiers docker-compose trouvés:"
echo "$compose_files"
echo ""

# Parcourir chaque fichier et chercher des références à "database"
for file in $compose_files; do
    echo "Analyse du fichier $file..."
    
    # Vérifier si le fichier contient une référence à "database"
    if grep -q "database:" "$file"; then
        echo "  - Référence à 'database' trouvée dans $file"
        
        # Créer une copie de sauvegarde
        backup_file="${file}.bak"
        cp "$file" "$backup_file"
        echo "  - Sauvegarde créée: $backup_file"
        
        # Créer une version temporaire avec les références à database commentées
        temp_file="${file}.temp"
        cat "$file" | sed '/database:/,/^[^ ]/s/^/#/' > "$temp_file"
        
        # Remplacer le fichier original
        mv "$temp_file" "$file"
        echo "  - Les références à 'database' ont été commentées dans $file"
    else
        echo "  - Aucune référence à 'database' trouvée dans $file"
    fi
    
    # Vérifier aussi les références à database dans des dépendances
    if grep -q "depends_on:.*database" "$file" || grep -q "links:.*database" "$file"; then
        echo "  - Référence à 'database' trouvée dans des dépendances ou liens"
        
        # S'assurer qu'il y a une sauvegarde
        if [ ! -f "$backup_file" ]; then
            cp "$file" "$backup_file"
            echo "  - Sauvegarde créée: $backup_file"
        fi
        
        # Commenter ou supprimer les références
        sed -i 's/\(depends_on:.*\)database\(.*\)/#\1database\2/' "$file"
        sed -i 's/\(links:.*\)database\(.*\)/#\1database\2/' "$file"
        echo "  - Les références à 'database' dans les dépendances ont été commentées"
    fi
done

echo ""
echo "==== INSTRUCTIONS ===="
echo "1. Tous les fichiers docker-compose ont été analysés et corrigés"
echo "2. Essayez maintenant de relancer vos commandes docker-compose:"
echo "   docker-compose down --volumes --remove-orphans"
echo "   docker-compose build --no-cache"
echo "   docker-compose up -d"
echo ""
echo "Si le problème persiste, utilisez le nouveau fichier docker-compose-new.yml avec la commande:"
echo "docker-compose -f docker-compose-new.yml up -d"
#!/bin/bash

echo "🔧 Correction complète de la configuration URL..."

# Vérifier et corriger l'URL dans .env
echo "📝 Mise à jour de l'URL dans .env..."
docker exec laravel_app sed -i 's|APP_URL=http://localhost|APP_URL=http://localhost:8080|g' .env
docker exec laravel_app sed -i 's|DB_HOST=127.0.0.1|DB_HOST=db|g' .env

# Vider tous les caches
echo "🗑️ Vidage des caches..."
docker exec laravel_app php artisan config:clear
docker exec laravel_app php artisan cache:clear
docker exec laravel_app php artisan view:clear
docker exec laravel_app php artisan route:clear

# Recréer le lien symbolique
echo "🔗 Recréation du lien symbolique..."
docker exec laravel_app rm -f public/storage
docker exec laravel_app php artisan storage:link

# Corriger les permissions
echo "🔐 Correction des permissions..."
docker exec laravel_app chmod -R 755 storage/app/public
docker exec laravel_app chown -R root:root storage/app/public

# Redémarrer les services
echo "🔄 Redémarrage des services..."
docker-compose restart nginx laravel_app

echo "✅ Configuration corrigée avec succès !"
echo ""
echo "🌐 Application accessible sur : http://localhost:8080"
echo "📊 phpMyAdmin accessible sur : http://localhost:8081"
echo "📈 SonarQube accessible sur : http://localhost:9000"

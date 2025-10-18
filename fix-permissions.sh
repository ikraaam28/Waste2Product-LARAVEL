#!/bin/bash

# Script pour corriger les permissions des fichiers uploadés
echo "🔧 Correction des permissions des fichiers uploadés..."

# Corriger les permissions du dossier storage
docker exec laravel_app chmod -R 755 storage/app/public
docker exec laravel_app chown -R root:root storage/app/public

# Corriger spécifiquement les dossiers d'upload
docker exec laravel_app chmod -R 755 storage/app/public/feedback-photos
docker exec laravel_app chmod -R 755 storage/app/public/profile_pictures
docker exec laravel_app chmod -R 755 storage/app/public/publications
docker exec laravel_app chmod -R 755 storage/app/public/products
docker exec laravel_app chmod -R 755 storage/app/public/events
docker exec laravel_app chmod -R 755 storage/app/public/tutos_media

echo "✅ Permissions corrigées avec succès !"
echo ""
echo "📁 Dossiers corrigés :"
echo "   - feedback-photos"
echo "   - profile_pictures"
echo "   - publications"
echo "   - products"
echo "   - events"
echo "   - tutos_media"

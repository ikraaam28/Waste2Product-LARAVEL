#!/bin/bash

# Script d'initialisation pour TeaHouse Laravel
echo "🚀 Initialisation de TeaHouse Laravel..."

# Attendre que la base de données soit prête
echo "⏳ Attente de la base de données MySQL..."
until docker exec mysql mysqladmin ping -h localhost --silent; do
    echo "En attente de MySQL..."
    sleep 2
done

echo "✅ Base de données MySQL prête !"

# Attendre que le conteneur Laravel soit prêt
echo "⏳ Attente du conteneur Laravel..."
sleep 5

# Exécuter les migrations
echo "🔄 Exécution des migrations..."
docker exec laravel_app php artisan migrate --force

# Exécuter les seeders
echo "🌱 Exécution des seeders..."
docker exec laravel_app php artisan db:seed --force

echo "✅ Initialisation terminée !"
echo ""
echo "📧 Email admin: admin@gmail.com"
echo "🔑 Mot de passe admin: admin123"
echo "🌐 Application disponible sur: http://localhost:8080"

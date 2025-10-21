#!/bin/bash

# Script pour créer l'utilisateur admin dans l'environnement Docker
echo "🚀 Démarrage de la création de l'utilisateur administrateur..."

# Attendre que la base de données soit prête
echo "⏳ Attente de la base de données..."
until docker exec mysql mysqladmin ping -h localhost --silent; do
    echo "En attente de MySQL..."
    sleep 2
done

echo "✅ Base de données prête !"

# Exécuter les migrations
echo "🔄 Exécution des migrations..."
docker exec laravel_app php artisan migrate --force

# Exécuter les seeders
echo "🌱 Exécution des seeders..."
docker exec laravel_app php artisan db:seed --force

echo "✅ Utilisateur administrateur créé avec succès !"
echo ""
echo "📧 Email: admin@gmail.com"
echo "🔑 Mot de passe: admin123"
echo ""
echo "🌐 Vous pouvez maintenant accéder à l'interface d'administration à l'adresse:"
echo "   http://localhost:8080"

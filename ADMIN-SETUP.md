# Configuration de l'utilisateur administrateur TeaHouse

## Utilisateur administrateur par défaut

Un utilisateur administrateur est automatiquement créé lors de l'exécution des seeders :

- **Email** : `admin@gmail.com`
- **Mot de passe** : `admin123`
- **Rôle** : `admin`

## Comment créer l'utilisateur admin

### Méthode 1 : Script automatique (recommandé)

```bash
# Rendre le script exécutable
chmod +x setup-admin.sh

# Exécuter le script
./setup-admin.sh
```

### Méthode 2 : Commandes manuelles

```bash
# Démarrer les conteneurs Docker
docker-compose up -d

# Attendre que la base de données soit prête, puis exécuter les migrations
docker exec laravel_app php artisan migrate --force

# Exécuter les seeders pour créer l'utilisateur admin
docker exec laravel_app php artisan db:seed --force
```

### Méthode 3 : Seeder spécifique

```bash
# Exécuter seulement le seeder admin
docker exec laravel_app php artisan db:seed --class=AdminUserSeeder
```

## Accès à l'interface d'administration

1. Assurez-vous que les conteneurs Docker sont démarrés :
   ```bash
   docker-compose up -d
   ```

2. Accédez à l'application via : `http://localhost:8080`

3. Connectez-vous avec les identifiants admin :
   - Email : `admin@gmail.com`
   - Mot de passe : `admin123`

## Sécurité

⚠️ **Important** : Changez le mot de passe par défaut après votre première connexion pour des raisons de sécurité.

## Dépannage

Si l'utilisateur admin n'est pas créé :

1. Vérifiez que la base de données est accessible :
   ```bash
   docker exec mysql mysqladmin ping -h localhost
   ```

2. Vérifiez les logs des conteneurs :
   ```bash
   docker-compose logs app
   docker-compose logs db
   ```

3. Réinitialisez la base de données si nécessaire :
   ```bash
   docker-compose down
   docker volume rm teahouse-laravel_db_data
   docker-compose up -d
   ./setup-admin.sh
   ```

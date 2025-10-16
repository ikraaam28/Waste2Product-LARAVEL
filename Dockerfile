# Choisir l'image PHP avec FPM
FROM php:8.2-fpm

# Installer les dépendances, pdo_mysql, apcu et netcat pour le wait loop
RUN apt-get update && apt-get install -y --no-install-recommends \
        default-libmysqlclient-dev libzip-dev zip unzip git netcat-openbsd \
    && pecl install apcu \
    && docker-php-ext-install pdo_mysql mysqli zip \
    && docker-php-ext-enable apcu \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Créer le répertoire de travail
WORKDIR /var/www

# Copier le code
COPY . .

# Installer les dépendances PHP
RUN composer install --no-interaction --optimize-autoloader

# Générer la clé de l'application si nécessaire
RUN php artisan key:generate

# Lancer PHP-FPM
CMD ["php-fpm"]

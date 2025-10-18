# Choisir l'image PHP avec FPM
FROM php:8.3-fpm

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git unzip curl libpng-dev libonig-dev libxml2-dev zip libzip-dev \
    libfreetype6-dev libjpeg62-turbo-dev libgd-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && pecl install pcov \
    && docker-php-ext-enable pcov

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

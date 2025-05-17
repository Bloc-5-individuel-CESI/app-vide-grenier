# Étape 1: Utiliser l'image officielle PHP avec Apache
FROM php:8.3-apache

# Étape 2: Installer les dépendances système et PHP
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    libicu-dev \
    curl \
    && docker-php-ext-install zip pdo pdo_mysql intl

# Étape 3: Installer Node.js et npm
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs

# Étape 4: Activer mod_rewrite pour Apache
RUN a2enmod rewrite

# Étape 5: Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Étape 6: Copier la configuration Apache
COPY docker/apache-config.conf /etc/apache2/sites-available/000-default.conf

# Étape 7: Définir le dossier de travail
WORKDIR /var/www/html

# Étape 8: Copier les fichiers du projet
COPY . .

# Étape 9: Fixer les permissions
RUN chown -R www-data:www-data /var/www/html/public/style && \
    chmod -R 775 /var/www/html/public/style

# Étape 10: Installer les dépendances PHP et JS + compiler le SCSS
RUN git config --global --add safe.directory /var/www/html && \
    composer install && \
    npm install && \
    npm run build

# Étape 11: Refixer les permissions
RUN chown -R www-data:www-data /var/www/html/

# Étape 12: Entrypoint
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

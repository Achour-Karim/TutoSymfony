# Utilisation de l'image officielle PHP avec Apache
FROM php:8.2-apache

# Installation des dépendances nécessaires
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    git \
    unzip \
    zip \
    && docker-php-ext-install \
    intl \
    pdo_mysql \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration d'Apache pour Symfony
RUN a2enmod rewrite

# Configuration du répertoire de travail
WORKDIR /var/www/html

# Copie du code source
COPY . .

# Installation des dépendances Symfony
RUN composer install

# Configuration des permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]

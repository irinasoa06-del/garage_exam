FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Installer extensions REQUISES
RUN apk add --no-cache \
    postgresql-dev \
    git \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Créer .env PAR DÉFAUT (sécurité)
RUN echo "APP_NAME=GarageAuto" > .env \
    && echo "APP_ENV=local" >> .env \
    && echo "APP_DEBUG=true" >> .env \
    && echo "APP_URL=http://localhost:8000" >> .env \
    && echo "DB_CONNECTION=pgsql" >> .env \
    && echo "DB_HOST=postgres" >> .env \
    && echo "DB_PORT=5432" >> .env \
    && echo "DB_DATABASE=garage_auto" >> .env \
    && echo "DB_USERNAME=garage_user" >> .env \
    && echo "DB_PASSWORD=garage_password" >> .env

# Commande de démarrage SIMPLE
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]

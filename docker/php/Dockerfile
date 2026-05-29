# docker/php/Dockerfile
FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    zip \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP necesarias para Symfony
RUN docker-php-ext-install pdo pdo_mysql intl opcache zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo
WORKDIR /var/www/html
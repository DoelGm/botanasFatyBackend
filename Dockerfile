FROM php:8.2-fpm

# Instalación de extensiones necesarias
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar archivos del proyecto
COPY . /var/www
WORKDIR /var/www

RUN composer install --optimize-autoloader --no-dev
RUN php artisan config:cache

CMD php artisan serve --host=0.0.0.0 --port=10000

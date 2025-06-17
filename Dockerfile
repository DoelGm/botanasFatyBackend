# Dockerfile

FROM php:8.2

# Dependencias necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Copiar archivos de la app
COPY . /var/www/html

WORKDIR /var/www/html

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalar dependencias de Laravel
RUN composer install --optimize-autoloader --no-dev

# Dar permisos al script de inicio
RUN chmod +x ./start.sh

# Puerto que Render usará
ENV PORT=10000

# Comando que se ejecuta al iniciar
CMD ["sh", "./start.sh"]

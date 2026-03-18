# Stage 1: Build frontend assets
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: PHP Application
FROM php:8.2-apache

# Install dependencies required for Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libxml2-dev \
    libonig-dev \
    unzip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring xml gd zip bcmath \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Fix DocumentRoot for exiting sites
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's!/var/www/!/var/www/html/public/!g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Copy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files First
COPY . .
COPY --from=frontend /app/public/build public/build

# Install Vendor Dependencies (ignore scripts to prevent DB connection errors during build)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Set permissions specifically for Laravel writable directories
RUN mkdir -p storage/logs storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Write a clean entrypoint script
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
PORT="${PORT:-8080}"\n\
echo "Listen ${PORT}" > /etc/apache2/ports.conf\n\
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf\n\
\n\
echo "Bootstrapping Laravel..."\n\
php artisan package:discover --ansi || true\n\
php artisan config:cache || true\n\
php artisan route:cache || true\n\
php artisan view:cache || true\n\
\n\
echo "Migrating Database..."\n\
php artisan migrate --force || true\n\
\n\
echo "Starting Apache on port ${PORT}..."\n\
exec apache2-foreground\n\
' > /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

CMD ["/usr/local/bin/entrypoint.sh"]

# Stage 1: Build frontend assets
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: PHP Application (CLI Base - No Apache)
FROM php:8.2-cli

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
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Copy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files First
COPY . .
COPY --from=frontend /app/public/build public/build

# Install Vendor Dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Set permissions
RUN mkdir -p storage/logs storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Write a clean entrypoint script
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
echo "Bootstrapping Laravel..."\n\
php artisan package:discover --ansi || true\n\
php artisan config:cache || true\n\
php artisan route:cache || true\n\
php artisan view:cache || true\n\
\n\
echo "Migrating Database..."\n\
php artisan migrate --force || true\n\
php artisan db:seed --force || true\n\
\n\
export PORT="${PORT:-8000}"\n\
echo "Starting Laravel server on port ${PORT}..."\n\
exec php artisan serve --host=0.0.0.0 --port=${PORT}\n\
' > /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

CMD ["/usr/local/bin/entrypoint.sh"]

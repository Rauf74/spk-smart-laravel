# Build frontend assets in an isolated Node stage so a clean deployment does
# not rely on generated files from a developer machine.
FROM node:20-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

# Dockerfile untuk Laravel di Render.com
FROM php:8.5-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Copy the versioned Vite assets produced by the build stage.
COPY --from=assets /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Configure Apache DocumentRoot
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Render injects the $PORT environment variable (default 10000). Apache must
# listen on that port or the health check / routing will fail.
EXPOSE 80

# Run outstanding migrations before Apache starts. APP_KEY and DATABASE_URL
# are supplied by Render environment variables, never baked into the image.
# Apache is reconfigured to listen on $PORT at runtime, and migrations are
# retried to survive a database that is not ready on a cold start.
CMD ["sh", "-c", "sed -i \"s/^Listen 80/Listen ${PORT:-80}/\" /etc/apache2/ports.conf && sed -i \"s/:80>/:${PORT:-80}>/\" /etc/apache2/sites-available/000-default.conf && for i in $(seq 1 15); do php artisan migrate --force && break || sleep 5; done && exec apache2-foreground"]

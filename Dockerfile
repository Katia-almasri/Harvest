FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev unzip curl git zip libgmp-dev \
    && docker-php-ext-install zip pdo pdo_mysql gmp

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy the application code
COPY . .

# Install Composer dependencies
RUN composer install --ignore-platform-reqs


# Fix permissions for Laravel's storage and cache directories
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

FROM php:8.2-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql \
    && apt-get clean

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs


COPY . .

RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

RUN composer install --no-dev --optimize-autoloader

RUN composer require laravel/breeze --dev && \
    php artisan breeze:install blade

RUN npm install

RUN chmod -R 755 node_modules

# Compile assets using Vite
RUN npm run build

# Clear and cache configurations, routes, and views for better performance
RUN php artisan config:clear && php artisan config:cache
RUN php artisan route:clear && php artisan route:cache
RUN php artisan view:clear && php artisan view:cache

# Ensure the Laravel storage and cache directories are writable
RUN chmod -R 775 storage bootstrap/cache

# Serve the application on 0.0.0.0 to make it accessible from outside the container
CMD php artisan serve --host=0.0.0.0 --port=8000

# Expose port 8000
EXPOSE 8000

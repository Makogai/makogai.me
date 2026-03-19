FROM php:8.4-fpm

# install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev nodejs npm \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# set working directory
WORKDIR /var/www

# copy project
COPY . .

# install dependencies
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# 🔥 FIX PERMISSIONS (CRITICAL)
RUN mkdir -p storage/logs \
 && chown -R www-data:www-data /var/www \
 && chmod -R 775 storage bootstrap/cache

# nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

# 🔥 RUNTIME COMMAND (IMPORTANT)
CMD php artisan config:clear \
 && php artisan route:clear \
 && php artisan view:clear \
 && php artisan migrate --force \
 && php artisan db:seed --force \
 && php artisan storage:link || true \
 && service nginx start \
 && php-fpm -F
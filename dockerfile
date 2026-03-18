FROM php:8.4-fpm

# install deps
RUN apt-get update && apt-get install -y \
    nginx \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev nodejs npm \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# install deps
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# permissions
RUN chown -R www-data:www-data /var/www \
 && chmod -R 775 storage bootstrap/cache

# nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

# 🔥 IMPORTANT: runtime setup
CMD php artisan config:clear \
 && php artisan route:clear \
 && php artisan view:clear \
 && php artisan migrate --force \
 && php artisan storage:link || true \
 && service nginx start \
 && php-fpm -F
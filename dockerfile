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

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

# permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

CMD service nginx start && php-fpm
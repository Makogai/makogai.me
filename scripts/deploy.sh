#!/bin/sh

echo "🚀 Starting Laravel deployment..."

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node deps & build frontend (Vite for Inertia Vue)
npm install
npm run build

# Generate app key if missing
if [ -z "$APP_KEY" ]; then
  echo "⚠️ APP_KEY missing, generating..."
  php artisan key:generate
fi

# Run migrations
php artisan migrate --force

# Cache for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage link
php artisan storage:link || true

# Fix permissions
chmod -R 775 storage bootstrap/cache

echo "✅ Deployment finished!"
#!/usr/bin/env bash
set -e

cd /var/www

# Ensure .env exists
if [ ! -f .env ]; then
  if [ -f .env.docker.example ]; then
    cp .env.docker.example .env
    echo "Copied .env from .env.docker.example"
  else
    echo "Warning: .env not found and .env.docker.example missing"
  fi
fi

# Install composer dependencies if not present
if [ ! -f vendor/autoload.php ]; then
  echo "Installing composer dependencies..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Generate app key if missing
APP_KEY=$(grep '^APP_KEY=' .env 2>/dev/null | cut -d'=' -f2- | tr -d '\r') || APP_KEY=""
if [ -z "$APP_KEY" ]; then
  echo "Generating APP_KEY..."
  php artisan key:generate --ansi
fi

# Publish Sanctum (idempotent)
echo "Publishing Sanctum and running migrations..."
php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider" --force || true

# Run migrations
php artisan migrate --force || true

# Start the built-in server
echo "Starting Laravel development server on 0.0.0.0:8000"
exec php artisan serve --host=0.0.0.0 --port=8000

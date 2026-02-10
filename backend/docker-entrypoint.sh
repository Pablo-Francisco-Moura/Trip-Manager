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

# If .env exists but appears configured for sqlite (local dev), prefer docker example DB settings
if [ -f .env ] && [ -f .env.docker.example ]; then
  # Read current DB_CONNECTION
  CUR_DB_CONN=$(grep '^DB_CONNECTION=' .env | cut -d'=' -f2- | tr -d '\r') || CUR_DB_CONN=""
  if [ "$CUR_DB_CONN" = "sqlite" ]; then
    echo "Detected sqlite in .env; applying docker DB settings from .env.docker.example"
    # Overwrite DB_* and APP_URL/SANCTUM settings from the docker example into .env
    for key in DB_CONNECTION DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD APP_URL SANCTUM_STATEFUL_DOMAINS SESSION_DOMAIN; do
      val=$(grep "^${key}=" .env.docker.example | cut -d'=' -f2-)
      if [ -n "$val" ]; then
        # remove existing key from .env
        sed -i "/^${key}=/d" .env || true
        # append new value
        echo "${key}=${val}" >> .env
      fi
    done
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

# If using sqlite and database file missing, create it so migrations/validation won't error
DB_CONN=$(grep '^DB_CONNECTION=' .env 2>/dev/null | cut -d'=' -f2- | tr -d '\r') || DB_CONN=""
if [ "$DB_CONN" = "sqlite" ]; then
  DB_FILE=$(grep '^DB_DATABASE=' .env 2>/dev/null | cut -d'=' -f2- | tr -d '\r') || DB_FILE="database/database.sqlite"
  # If DB_DATABASE is empty or a relative path, default to database/database.sqlite
  if [ -z "$DB_FILE" ]; then
    DB_FILE="database/database.sqlite"
  fi
  # make sure directory exists
  mkdir -p "$(dirname "$DB_FILE")"
  if [ ! -f "$DB_FILE" ]; then
    touch "$DB_FILE" || true
    echo "Created sqlite database file at $DB_FILE"
  fi
fi

# Publish Sanctum (idempotent)
echo "Publishing Sanctum and running migrations..."
php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider" --force || true

# Run migrations
php artisan migrate --force || true

# Seed database (idempotent seeder)
echo "Running database seed (DatabaseSeeder)..."
php artisan db:seed --class=DatabaseSeeder --force || true

# Start the built-in server
echo "Starting Laravel development server on 0.0.0.0:8000"
exec php artisan serve --host=0.0.0.0 --port=8000

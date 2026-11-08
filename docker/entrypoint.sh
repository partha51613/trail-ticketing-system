#!/bin/sh
set -e

# Copy .env if not present
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Ensure SQLite file exists if using sqlite
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ] || grep -q "DB_CONNECTION=sqlite" .env 2>/dev/null; then
    mkdir -p database
    if [ ! -f database/database.sqlite ]; then
        touch database/database.sqlite
    fi
    chmod 666 database/database.sqlite 2>/dev/null || true
    chmod 777 database 2>/dev/null || true
fi

# Ensure cache and storage directories exist and are writable without altering tracked files
mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chmod -R 777 storage/framework storage/logs bootstrap/cache 2>/dev/null || true
find storage/app -type d -exec chmod 777 {} + 2>/dev/null || true

# Generate APP_KEY if empty or not set
if grep -q "APP_KEY=$" .env 2>/dev/null || grep -q "APP_KEY=\s*$" .env 2>/dev/null || [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run migrations & seeders
if [ "$DB_FRESH_SEED" = "true" ]; then
    echo "==> Running php artisan migrate:fresh..."
    php artisan migrate:fresh --force
    echo "==> Running php artisan db:seed..."
    php artisan db:seed --force
else
    echo "==>Kindly set DB_FRESH_SEED=true in the docker-compose.yml"
    echo "==>OR go inside the docker container and run the two commands manually from inside the 'app' folder"
    echo "==> php artisan migrate:fresh"
    echo "==> php artisan db:seed"
fi

# Optimize configs & routes
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start Laravel development server
echo "==> Starting Trail Application on http://0.0.0.0:8000"
exec php artisan serve --host=0.0.0.0 --port=8000

#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

if [[ ! -f artisan ]]; then
  echo "error: Laravel application not found under ./laravel (expected artisan)." >&2
  echo "Create the app with: composer create-project laravel/laravel laravel" >&2
  exit 1
fi

if [[ ! -f .env ]]; then
  cp /opt/laravel/.env.example .env
fi

if [[ ! -d vendor ]]; then
  composer install --no-interaction --prefer-dist
fi

if [[ ! -f public/build/manifest.json ]]; then
  echo "Building frontend assets..."
  if [[ -f package-lock.json ]]; then
    npm ci
  else
    npm install
  fi
  npm run build
fi

if ! grep -qE '^APP_KEY=base64:.+' .env 2>/dev/null; then
  php artisan key:generate --force --no-interaction
fi

mkdir -p storage/framework/{sessions,views,cache/data} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

host="${DB_HOST:-mysql}"
port="${DB_PORT:-3306}"
user="${DB_USERNAME:-laravel}"
pass="${DB_PASSWORD:-laravel}"

echo "Waiting for MySQL at ${host}:${port}..."
for i in $(seq 1 60); do
  if mysqladmin ping -h"$host" -P"$port" -u"$user" -p"$pass" --silent 2>/dev/null; then
    echo "MySQL is up."
    break
  fi
  if [[ "$i" -eq 60 ]]; then
    echo "MySQL did not become ready in time." >&2
    exit 1
  fi
  sleep 2
done

if [[ "${CONTAINER_ROLE:-app}" == "app" && "${RUN_MIGRATIONS:-true}" != "false" ]]; then
  php artisan migrate --force --no-interaction
fi

exec docker-php-entrypoint "$@"

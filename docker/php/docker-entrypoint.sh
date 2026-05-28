#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

if [[ ! -f artisan ]]; then
  echo "error: Laravel application not found under ./laravel (expected artisan)." >&2
  echo "Create the app with: composer create-project laravel/laravel laravel" >&2
  exit 1
fi

mkdir -p \
  storage/app/public \
  storage/framework/sessions \
  storage/framework/views \
  storage/framework/cache/data \
  storage/framework/testing \
  storage/logs \
  bootstrap/cache
touch storage/logs/laravel.log

if [[ ! -d vendor || -z "$(ls -A vendor 2>/dev/null)" ]]; then
  echo "Restoring vendor/ from image baseline..."
  cp -a /opt/laravel-build/vendor /var/www/html/vendor
fi

if [[ ! -d node_modules || -z "$(ls -A node_modules 2>/dev/null)" ]]; then
  echo "Restoring node_modules/ from image baseline..."
  cp -a /opt/laravel-build/node_modules /var/www/html/node_modules
fi

if [[ ! -f public/build/manifest.json ]]; then
  echo "Restoring public/build from image baseline..."
  mkdir -p public
  rm -rf public/build
  cp -a /opt/laravel-build/public/build /var/www/html/public/build
fi

if [[ ! -f .env ]]; then
  cp /opt/laravel/.env.example .env
fi

chown -R www-data:www-data storage bootstrap/cache .env 2>/dev/null || true
chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true

if ! grep -qE '^APP_KEY=base64:.+' .env 2>/dev/null; then
  gosu www-data php artisan key:generate --force --no-interaction
fi

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
  gosu www-data php artisan migrate --force --no-interaction
fi

if [[ "${1:-}" == "php-fpm" ]]; then
  exec docker-php-entrypoint "$@"
else
  exec gosu www-data docker-php-entrypoint "$@"
fi

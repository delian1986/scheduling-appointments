# Scheduling appointments — Docker (Laravel)

Local stack: **nginx**, **PHP 8.4-FPM**, **MySQL 8**, **phpMyAdmin**, **Redis**, and a **Laravel** app in [`laravel/`](laravel/).

## Requirements

- Docker Engine with Compose v2

## First run

From the repository root:

```bash
docker compose build
docker compose up -d
```

On the first start, the **php** service entrypoint will:

1. Create [`laravel/.env`](laravel/.env) from the root [`.env.example`](.env.example) baked into the image at build time (if `.env` is missing).
2. Run `composer install` if `vendor/` is missing.
3. Run `npm ci && npm run build` if Vite assets are missing (common with the bind-mounted `laravel/` directory).
4. Run `php artisan key:generate` when `APP_KEY` is empty.
5. Ensure `storage/` and `bootstrap/cache/` are writable by PHP-FPM (`www-data`).
6. Wait for MySQL, then run `php artisan migrate --force`.

The **php** image build runs `composer install` and `npm run build` so production-style images include compiled assets.

## URLs and ports (defaults)

| Service     | URL / port |
|------------|------------|
| Laravel app | http://localhost:8080 |
| phpMyAdmin | http://localhost:8081 |
| MySQL      | `127.0.0.1:33060` (user/password/database from env below) |
| Redis      | `127.0.0.1:63790` |

Override ports and DB credentials by copying [`.env.example`](.env.example) to **`.env`** at the repo root (Compose reads this file for variable substitution). The same template is used inside the PHP image for Laravel’s `.env` when the container creates it.

Default database: **database** `laravel`, **user** `laravel`, **password** `laravel`, **root password** `root`.

## Common commands

```bash
docker compose logs -f php
docker compose exec -w /var/www/html php php artisan migrate
docker compose exec -w /var/www/html php composer require some/package
docker compose down
```

If Laravel cannot write logs or cache (empty `storage/logs/laravel.log`, HTTP 500), fix permissions on a running stack:

```bash
docker compose exec php chown -R www-data:www-data storage bootstrap/cache
docker compose exec php chmod -R ug+rwx storage bootstrap/cache
```

Rebuild frontend assets inside the running container:

```bash
docker compose exec -w /var/www/html php npm ci
docker compose exec -w /var/www/html php npm run build
```

## Project layout

- [`docker-compose.yml`](docker-compose.yml) — services and healthchecks.
- [`docker/php/Dockerfile`](docker/php/Dockerfile) — PHP extensions, Composer, copies [`.env.example`](.env.example) into `/opt/laravel/` at build.
- [`docker/php/docker-entrypoint.sh`](docker/php/docker-entrypoint.sh) — Laravel bootstrap before `php-fpm`.
- [`docker/nginx/default.conf`](docker/nginx/default.conf) — nginx → PHP-FPM.

## Appointments API

List appointments (paginated, newest scheduled first; soft-deleted rows are excluded):

```bash
curl http://localhost:8080/api/appointments \
  -H "Accept: application/json"
```

Optional query: `?page=2` (15 per page by default). Response includes `data`, `links`, and `meta`.

Create an appointment:

```bash
curl -X POST http://localhost:8080/api/appointments \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "full_name": "Ivan Ivanov",
    "ucn": "1234567890",
    "description": "Initial consultation",
    "scheduled_at": "2026-06-01 10:00:00",
    "notification_method": "email",
    "email": "ivan@example.com"
  }'
```

Send `Accept: application/json` so validation errors return JSON (`422`) instead of a web redirect.

Web UI:

- `GET /` — paginated appointments list (scheduled date, client name; Edit/Delete buttons are placeholders)
- `GET /appointments/add` — add appointment form
- `POST /appointments/add` — create appointment (redirects to list with success flash)

## Notes

- `migrate --force` is intended for **local Docker** only; do not use this pattern blindly in production.
- [`laravel/vendor/`](laravel/vendor/) is gitignored; dependencies are installed in the container (or via `composer install` on the host with a full PHP stack).

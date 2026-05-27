# Scheduling appointments — Docker (Laravel)

Local stack: **nginx**, **PHP 8.3-FPM**, **MySQL 8**, **phpMyAdmin**, **Redis**, and a **Laravel** app in [`laravel/`](laravel/).

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
3. Run `php artisan key:generate` when `APP_KEY` is empty.
4. Wait for MySQL, then run `php artisan migrate --force`.

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

## Project layout

- [`docker-compose.yml`](docker-compose.yml) — services and healthchecks.
- [`docker/php/Dockerfile`](docker/php/Dockerfile) — PHP extensions, Composer, copies [`.env.example`](.env.example) into `/opt/laravel/` at build.
- [`docker/php/docker-entrypoint.sh`](docker/php/docker-entrypoint.sh) — Laravel bootstrap before `php-fpm`.
- [`docker/nginx/default.conf`](docker/nginx/default.conf) — nginx → PHP-FPM.

## Notes

- `migrate --force` is intended for **local Docker** only; do not use this pattern blindly in production.
- [`laravel/vendor/`](laravel/vendor/) is gitignored; dependencies are installed in the container (or via `composer install` on the host with a full PHP stack).

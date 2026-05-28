# syntax=docker/dockerfile:1.7

FROM node:22-bookworm AS frontend

WORKDIR /app

COPY laravel/package.json laravel/package-lock.json ./
RUN npm ci

COPY laravel/ .
RUN npm run build

FROM php:8.4-fpm-bookworm

ARG UID=1000
ARG GID=1000

RUN apt-get update && apt-get install -y --no-install-recommends \
        default-mysql-client \
        git \
        unzip \
        gosu \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libicu-dev \
        libonig-dev \
        libxml2-dev \
        libcurl4-openssl-dev \
        $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        dom \
        xml \
        pdo_mysql \
        mbstring \
        pcntl \
        bcmath \
        gd \
        intl \
        zip \
        opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/* \
    && gosu nobody true

RUN set -eux; \
    if [ "$(id -g www-data)" != "${GID}" ]; then \
        existing_group="$(getent group "${GID}" | cut -d: -f1 || true)"; \
        if [ -n "${existing_group}" ] && [ "${existing_group}" != "www-data" ]; then \
            groupmod -g 9999 "${existing_group}"; \
        fi; \
        groupmod -g "${GID}" www-data; \
    fi; \
    if [ "$(id -u www-data)" != "${UID}" ]; then \
        existing_user="$(getent passwd "${UID}" | cut -d: -f1 || true)"; \
        if [ -n "${existing_user}" ] && [ "${existing_user}" != "www-data" ]; then \
            usermod -u 9999 "${existing_user}"; \
        fi; \
        usermod -u "${UID}" -g "${GID}" www-data; \
    fi

COPY --from=frontend /usr/local/bin/node /usr/local/bin/node
COPY --from=frontend /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -sf /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
 && ln -sf /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY .env.example /opt/laravel/.env.example

WORKDIR /var/www/html

COPY laravel/composer.json laravel/composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts --no-autoloader

COPY laravel/ .
RUN composer install --no-interaction --prefer-dist

COPY --from=frontend /app/public/build ./public/build
COPY --from=frontend /app/node_modules ./node_modules

RUN set -eux; \
    mkdir -p \
        storage/app/public \
        storage/framework/sessions \
        storage/framework/views \
        storage/framework/cache/data \
        storage/framework/testing \
        storage/logs \
        bootstrap/cache; \
    touch storage/logs/laravel.log; \
    chown -R www-data:www-data /var/www/html /opt/laravel; \
    chmod -R ug+rwX storage bootstrap/cache

RUN set -eux; \
    mkdir -p /opt/laravel-build/public; \
    cp -a /var/www/html/vendor       /opt/laravel-build/vendor; \
    cp -a /var/www/html/node_modules /opt/laravel-build/node_modules; \
    cp -a /var/www/html/public/build /opt/laravel-build/public/build; \
    chown -R www-data:www-data /opt/laravel-build

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]

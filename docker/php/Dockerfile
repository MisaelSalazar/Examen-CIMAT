# syntax=docker/dockerfile:1

###############################################################################
# 1. Dependencias PHP de producción
###############################################################################
FROM composer:2 AS vendor

WORKDIR /app

COPY src/composer.json src/composer.lock ./

RUN composer install \
        --no-dev \
        --no-interaction \
        --no-progress \
        --prefer-dist \
        --no-scripts \
        --optimize-autoloader

###############################################################################
# 2. Dependencias PHP de desarrollo (solo para el target "testing")
###############################################################################
FROM composer:2 AS vendor-dev

WORKDIR /app

COPY src/composer.json src/composer.lock ./

RUN composer install \
        --no-interaction \
        --no-progress \
        --prefer-dist \
        --no-scripts \
        --optimize-autoloader

###############################################################################
# 3. Compilación de assets (Tailwind + Vite)
###############################################################################
FROM node:24-alpine AS assets

WORKDIR /app

COPY src/package.json src/package-lock.json src/.npmrc ./

RUN npm ci --no-audit --no-fund

COPY src/vite.config.js ./
COPY src/resources ./resources
COPY src/public ./public

RUN npm run build

###############################################################################
# 4. Runtime: PHP-FPM (sin privilegios)
###############################################################################
FROM php:8.5-fpm-alpine AS runtime

RUN apk add --no-cache \
        icu-libs \
        libzip \
        oniguruma \
        sqlite-libs \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
        sqlite-dev \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        intl \
        pdo_mysql \
        pdo_sqlite \
        zip \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/* /tmp/*

COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-app.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-app.conf
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint

RUN chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www/html

COPY --chown=www-data:www-data src/ ./
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor
COPY --from=assets --chown=www-data:www-data /app/public/build ./public/build

RUN mkdir -p \
        bootstrap/cache \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

USER www-data

EXPOSE 9000

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD php artisan --version >/dev/null 2>&1 || exit 1

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]

###############################################################################
# 5. Runtime de pruebas (PHPUnit, SQLite en memoria)
###############################################################################
FROM runtime AS testing

USER root

COPY --from=vendor-dev --chown=www-data:www-data /app/vendor ./vendor

# phpdotenv emite una advertencia si no existe .env; se crea uno mínimo para
# que `php artisan test` corra sin ruido. Las variables de compose y
# phpunit.xml tienen prioridad sobre estos valores.
RUN printf '%s\n' \
        'APP_NAME="Aspirantes CIMAT"' \
        'APP_ENV=testing' \
        'APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=' \
        'APP_DEBUG=true' \
        'APP_LOCALE=es' \
        'APP_TIMEZONE=America/Mexico_City' \
        'DB_CONNECTION=sqlite' \
        'DB_DATABASE=:memory:' \
        'CACHE_STORE=array' \
        'SESSION_DRIVER=array' \
        'QUEUE_CONNECTION=sync' \
        'LOG_CHANNEL=stderr' \
        > .env \
    && chown www-data:www-data .env

USER www-data

ENV APP_ENV=testing

ENTRYPOINT []
CMD ["php", "artisan", "test"]

###############################################################################
# 6. Servidor web: Nginx
###############################################################################
FROM nginx:1.30-alpine AS web

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY src/public /var/www/html/public
COPY --from=assets /app/public/build /var/www/html/public/build

RUN chown -R nginx:nginx /var/www/html

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD wget -q --spider http://127.0.0.1/up || exit 1

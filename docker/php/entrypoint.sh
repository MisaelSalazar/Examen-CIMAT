#!/bin/sh
#
# Arranque de la aplicación:
#   1. Espera a que la base de datos esté disponible.
#   2. Genera APP_KEY si no se proporcionó una.
#   3. Aplica migraciones y, opcionalmente, datos de demostración.
#   4. Cachea configuración, rutas y vistas.
#
set -eu

cd /var/www/html

wait_for_database() {
    if [ "${DB_CONNECTION:-mariadb}" = "sqlite" ]; then
        return 0
    fi

    intento=1
    while ! php -r '
        $host = getenv("DB_HOST") ?: "127.0.0.1";
        $port = getenv("DB_PORT") ?: "3306";
        $name = getenv("DB_DATABASE") ?: "";
        try {
            new PDO("mysql:host={$host};port={$port};dbname={$name}", getenv("DB_USERNAME") ?: "", getenv("DB_PASSWORD") ?: "");
        } catch (Throwable $e) {
            fwrite(STDERR, $e->getMessage().PHP_EOL);
            exit(1);
        }
    ' 2>/dev/null; do
        if [ "$intento" -ge 30 ]; then
            echo "[entrypoint] No fue posible conectar con la base de datos tras 60 segundos." >&2
            exit 1
        fi

        echo "[entrypoint] Esperando a la base de datos (${intento}/30)…"
        intento=$((intento + 1))
        sleep 2
    done
}

wait_for_database

if [ -z "${APP_KEY:-}" ]; then
    APP_KEY="$(php artisan key:generate --show --no-ansi)"
    export APP_KEY
    echo "[entrypoint] APP_KEY no definida: se generó una clave efímera para este contenedor."
fi

php artisan package:discover --ansi

echo "[entrypoint] Aplicando migraciones…"
php artisan migrate --force --no-interaction

if [ "${SEED_DB:-false}" = "true" ]; then
    echo "[entrypoint] Cargando datos de demostración…"
    php artisan db:seed --force --no-interaction
fi

php artisan optimize:clear --no-interaction >/dev/null 2>&1 || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[entrypoint] Aplicación lista."

exec "$@"

#!/bin/sh
set -eu

port="${PORT:-10000}"

case "$port" in
    ''|*[!0-9]*)
        echo "PORT must be a numeric value." >&2
        exit 64
        ;;
esac

sed -ri "s/^Listen [0-9]+$/Listen ${port}/" /etc/apache2/ports.conf
sed -ri "s#<VirtualHost \*:[0-9]+>#<VirtualHost *:${port}>#" /etc/apache2/sites-available/000-default.conf

mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache

if [ ! -L public/storage ]; then
    php artisan storage:link
fi

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force --no-interaction
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec docker-php-entrypoint "$@"

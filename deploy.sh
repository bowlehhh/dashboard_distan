#!/usr/bin/env bash

set -Eeuo pipefail

if [[ ! -f artisan || ! -f .env ]]; then
    echo 'Jalankan skrip ini dari root aplikasi Laravel yang sudah memiliki .env produksi.' >&2
    exit 1
fi

deployment_environment=$(sed -n 's/^APP_ENV=//p' .env | head -n 1)

if [[ "$deployment_environment" != 'production' ]]; then
    echo 'Deploy dibatalkan: APP_ENV pada .env harus production.' >&2
    exit 1
fi

mkdir -p storage/framework
exec 9>storage/framework/deploy.lock

if ! flock -n 9; then
    echo 'Deploy lain masih berjalan.' >&2
    exit 1
fi

is_down=false

restore_application() {
    if [[ "$is_down" == true ]]; then
        php artisan up || true
    fi
}

trap restore_application EXIT

php artisan down --retry=60
is_down=true

composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
php artisan optimize:clear
php artisan migrate --force --no-interaction
php artisan storage:link --force
npm ci
npm run build
php artisan config:cache
php artisan view:cache

php artisan up
is_down=false

echo 'Deploy selesai. Pastikan web server menggunakan document root: public.'

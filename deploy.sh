#!/usr/bin/env bash

set -Eeuo pipefail

if [[ ! -f artisan || ! -f .env ]]; then
    echo 'Jalankan skrip ini dari root aplikasi Laravel yang sudah memiliki .env produksi.' >&2
    exit 1
fi

read_env_value() {
    local key="$1"
    local value

    value=$(sed -n "s/^${key}=//p" .env | tail -n 1 | tr -d '\r')
    value="${value#\"}"
    value="${value%\"}"
    value="${value#\'}"
    value="${value%\'}"

    printf '%s' "$value"
}

deployment_environment=$(read_env_value APP_ENV)
deployment_debug=$(read_env_value APP_DEBUG)
deployment_key=$(read_env_value APP_KEY)
deployment_url=$(read_env_value APP_URL)
admin_email=$(read_env_value SIMANTAP_ADMIN_EMAIL)
admin_password=$(read_env_value SIMANTAP_ADMIN_PASSWORD)
session_secure_cookie=$(read_env_value SESSION_SECURE_COOKIE)
mail_mailer=$(read_env_value MAIL_MAILER)

if [[ "$deployment_environment" != 'production' ]]; then
    echo 'Deploy dibatalkan: APP_ENV pada .env harus production.' >&2
    exit 1
fi

if [[ "$deployment_debug" != 'false' ]]; then
    echo 'Deploy dibatalkan: APP_DEBUG pada .env harus false.' >&2
    exit 1
fi

if [[ -z "$deployment_key" ]]; then
    echo 'Deploy dibatalkan: APP_KEY belum diisi.' >&2
    exit 1
fi

if [[ -z "$deployment_url" || "$deployment_url" == *'localhost'* || "$deployment_url" == *'127.0.0.1'* ]]; then
    echo 'Deploy dibatalkan: APP_URL harus menggunakan alamat server produksi.' >&2
    exit 1
fi

if [[ -z "$admin_email" || "$admin_email" != *@* || "$admin_email" == 'admin@simantap.example' ]]; then
    echo 'Deploy dibatalkan: SIMANTAP_ADMIN_EMAIL belum valid.' >&2
    exit 1
fi

if (( ${#admin_password} < 12 )) || [[ "$admin_password" == 'ubah-dengan-password-kuat' ]]; then
    echo 'Deploy dibatalkan: SIMANTAP_ADMIN_PASSWORD harus minimal 12 karakter.' >&2
    exit 1
fi

if [[ "$deployment_url" != https://* ]]; then
    echo 'Peringatan: APP_URL belum menggunakan HTTPS.' >&2
fi

if [[ "$session_secure_cookie" != 'true' ]]; then
    echo 'Peringatan: SESSION_SECURE_COOKIE sebaiknya true pada server HTTPS.' >&2
fi

if [[ "$mail_mailer" == 'log' || -z "$mail_mailer" ]]; then
    echo 'Peringatan: MAIL_MAILER belum dapat mengirim email reset password.' >&2
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
php artisan db:seed --class=DatabaseSeeder --force --no-interaction
php artisan storage:link --force
npm ci
npm run build
php artisan optimize
php artisan queue:restart

php artisan up
is_down=false

echo 'Deploy selesai. Pastikan web server menggunakan document root: public.'

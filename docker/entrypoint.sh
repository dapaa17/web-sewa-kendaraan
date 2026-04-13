#!/usr/bin/env bash
set -euo pipefail

APP_DIR="/var/www/html"
STORAGE_LINK="${APP_DIR}/storage"
PERSISTENT_STORAGE_PATH="${PERSISTENT_STORAGE_PATH:-}"

if [ -n "${PERSISTENT_STORAGE_PATH}" ]; then
    echo "Preparing persistent storage at ${PERSISTENT_STORAGE_PATH}"
    mkdir -p "${PERSISTENT_STORAGE_PATH}"

    if [ ! -L "${STORAGE_LINK}" ]; then
        if [ -d "${STORAGE_LINK}" ]; then
            cp -a "${STORAGE_LINK}/." "${PERSISTENT_STORAGE_PATH}/" || true
            rm -rf "${STORAGE_LINK}"
        fi

        ln -s "${PERSISTENT_STORAGE_PATH}" "${STORAGE_LINK}"
    fi
fi

mkdir -p \
    "${APP_DIR}/storage/logs" \
    "${APP_DIR}/storage/app/public" \
    "${APP_DIR}/storage/framework/cache/data" \
    "${APP_DIR}/storage/framework/sessions" \
    "${APP_DIR}/storage/framework/views" \
    "${APP_DIR}/bootstrap/cache"

chmod -R ug+rwX "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache" || true

echo "Bootstrapping Laravel..."
php artisan package:discover --ansi || true
php artisan storage:link || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Migrating Database..."
php artisan migrate --force || true

export PORT="${PORT:-8000}"
echo "Starting Laravel server on port ${PORT}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT}"

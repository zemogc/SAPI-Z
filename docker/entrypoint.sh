#!/bin/sh
set -e

# Primer arranque: crea .env, la clave y la base SQLite con datos de demostración.
[ -f .env ] || cp .env.example .env
grep -q '^APP_KEY=base64' .env || php artisan key:generate --force

# `artisan serve` solo pasa a sus workers lo que está en .env, así que se copian ahí.
for var in DB_DATABASE ANTHROPIC_API_KEY ANTHROPIC_MODEL ANTHROPIC_EFFORT; do
    eval "valor=\${$var:-}"
    if [ -n "$valor" ]; then
        sed -i "/^#\? *$var=/d" .env
        echo "$var=$valor" >> .env
    fi
done

DB_FILE="${DB_DATABASE:-database/database.sqlite}"

if [ ! -f "$DB_FILE" ]; then
    mkdir -p "$(dirname "$DB_FILE")"
    touch "$DB_FILE"
    php artisan migrate --seed --force
else
    php artisan migrate --force
fi

exec php artisan serve --host=0.0.0.0 --port=8000

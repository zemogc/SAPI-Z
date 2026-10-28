# Compila los assets de Vite
FROM node:22-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
# El lock se generó en Windows: se agrega el binario nativo de rollup para Linux.
RUN npm ci \
    && npm install --no-save "@rollup/rollup-linux-x64-gnu@$(node -p "require('rollup/package.json').version")"
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
COPY app ./app
RUN npm run build

# Aplicación PHP
FROM php:8.3-cli
RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libzip-dev \
    && docker-php-ext-install zip bcmath \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-progress --no-scripts --prefer-dist

COPY . .
COPY --from=assets /app/public/build ./public/build
RUN composer dump-autoload --optimize \
    && chmod +x docker/entrypoint.sh

EXPOSE 8000
ENTRYPOINT ["docker/entrypoint.sh"]

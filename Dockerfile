FROM php:8.3-cli-alpine AS base

RUN apk add --no-cache \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    autoconf \
    g++ \
    make \
    && docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    zip \
    bcmath \
    pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del autoconf g++ make

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/backend

# --- development ---
FROM base AS development

COPY backend/composer.json ./
RUN composer install --no-scripts --no-autoloader --no-interaction

COPY backend/ .
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && mkdir -p storage/logs \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/testing \
        storage/framework/views \
        bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache \
    && composer dump-autoload --optimize

EXPOSE 8000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# --- production ---
FROM base AS production

COPY backend/composer.json ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --optimize-autoloader

COPY backend/ .
RUN mkdir -p storage/logs \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data /var/www/backend \
    && composer dump-autoload --optimize --classmap-authoritative

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

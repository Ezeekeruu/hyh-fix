# HYH Fix on Render (free tier) + Supabase Postgres.
# Build: docker build . / Deploy: Render Blueprint from this repo.

FROM php:8.2-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev unzip git \
    && docker-php-ext-install pdo_pgsql pgsql bcmath zip \
    && a2enmod rewrite \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

# Boot: link storage (ephemeral disk, recreated every boot), migrate,
# cache config/routes/views, then serve. All values come from Render env vars.
CMD ["sh", "-c", "php artisan storage:link > /dev/null 2>&1 || true; php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && exec apache2-foreground"]

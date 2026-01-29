FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git unzip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev \
    libpq-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install gd pdo pdo_pgsql zip \
  && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN mkdir -p storage/logs bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache \
 && ln -sf /dev/stderr storage/logs/laravel.log

RUN php artisan config:clear || true \
 && php artisan cache:clear || true \
 && php artisan route:clear || true \
 && php artisan view:clear || true

CMD php -S 0.0.0.0:$PORT -t public
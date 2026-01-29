FROM php:8.4-cli

# System deps + PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip curl ca-certificates \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev \
    libpq-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install gd pdo pdo_pgsql zip \
  && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Install deps
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Writable dirs + log to stderr (biar kebaca di Railway Logs)
RUN mkdir -p storage/logs bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache \
 && ln -sf /dev/stderr storage/logs/laravel.log

# IMPORTANT: jangan jalanin artisan clear/cache di build-time.
# Railway inject env di runtime; kita clear di startup.

CMD sh -lc "\
  php artisan config:clear || true && \
  php artisan cache:clear || true && \
  php artisan route:clear || true && \
  php artisan view:clear || true && \
  php -S 0.0.0.0:$PORT -t public \
"
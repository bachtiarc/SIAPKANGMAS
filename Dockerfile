FROM php:8.4-cli

# Install system deps + PHP extensions (gd + pgsql + zip)
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libjpeg-dev libfreetype6-dev libzip-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install gd pdo pdo_pgsql zip \
  && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Install PHP deps
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Serve Laravel (simple server)
CMD php -S 0.0.0.0:$PORT -t public

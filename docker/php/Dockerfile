FROM docker.io/library/php:8.4-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=docker.io/library/composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Pre-install vendor saat build (disimpan di vendor_data named volume)
# Layer ini di-cache selama composer.json/lock tidak berubah
COPY composer.json composer.lock ./
# Pakai update bukan install agar tetap jalan meski composer.lock tidak sinkron dengan composer.json
RUN composer update --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy entrypoint
COPY docker/php/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]

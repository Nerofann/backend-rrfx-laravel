# syntax=docker/dockerfile:1.7

###################################
# 1️⃣ Base PHP Image
###################################
FROM php:8.1-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    bash \
    curl \
    git \
    unzip \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        intl \
        gd \
        opcache

###################################
# 2️⃣ Install Composer
###################################
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

###################################
# 3️⃣ Install PHP Dependencies (Cache Layer)
###################################
COPY composer.json composer.lock ./
COPY app/Helpers ./app/Helpers
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist

###################################
# 4️⃣ Copy Project Files
###################################
COPY . .


###################################
# 5️⃣ Laravel Optimizations
###################################
RUN php artisan package:discover --ansi \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

###################################
# 6️⃣ Permissions
###################################
RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache

USER www-data

EXPOSE 3099
CMD ["php-fpm"]

#image PHP 8.3 + Nginx + Laravel

#/* cspell:disable */

FROM php:8.3-fpm-alpine AS base

# Extensions PHP nécessaires
RUN apk add --no-cache \
        nginx \
        supervisor \
        libpq-dev \
        libzip-dev \
        zip \
        unzip \
        curl \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        zip \
        opcache \
        pcntl 
    
# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copier d'abord composer.json pour tirer parti du cache Docker
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copier le reste de l'application
COPY . .

# Générer l'autoloader final avec les scripts
RUN composer run-script post-autoload-dump

# Permissions correctes
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copier les configs
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8000


CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
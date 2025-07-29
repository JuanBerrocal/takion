# This is a Dockerfile to be deplyed in render

# First stage
FROM composer:2 AS build

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader

# Second stage
FROM php:8.4.6-fpm

RUN echo "listen = 127.0.0.1:9000" >> /usr/local/etc/php-fpm.d/zz-docker.conf

# Extensions needed for Symfony
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    git unzip curl libpq-dev libzip-dev zip libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install pdo pdo_mysql zip

WORKDIR /var/www/html

COPY --from=build /app /var/www/html

RUN rm /etc/nginx/sites-enabled/default

COPY .docker/render/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY .docker/render/default.conf /etc/nginx/conf.d/default.conf
COPY .docker/render/supervisor.conf /etc/supervisord.conf

# Gives permissions to www-data user (symfony).
RUN chown -R www-data:www-data /var/www/html/var \
    && chmod -R 755 /var/www/html/var
RUN mkdir -p /var/www/html/var/log && chown -R www-data:www-data /var/www/html/var/log

# Expose port 80 to serve HTML
EXPOSE 80

# Launch nginx and php together
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]


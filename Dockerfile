# This is a Dockerfile to be deplyed in render

# First stage
FROM php:8.4.6-fpm AS build

# Extensions needed for Symfony and composer
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libzip-dev zip libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Installs composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Copies configuration files
COPY composer.json composer.lock ./

# Installs PHP dependencies.  It doesnt execute scripts at the first stage.
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-scripts

# Copies the rest.
COPY . .

# Build Symfony cache here (NO .env)
RUN APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear --no-warmup

# Second stage
FROM php:8.4.6-fpm

# Extensions needed for Symfony and composer
RUN apt-get update && apt-get install -y \
    nginx supervisor git unzip curl libpq-dev libzip-dev zip libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

RUN echo "listen = 127.0.0.1:9000" >> /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /var/www/html

COPY --from=build /app /var/www/html

# composer doesnt exists any longer at this stage, so these lines will fail.
# RUN COMPOSER_ALLOW_SUPERUSER=1 composer run-script post-install-cmd || true
# RUN php bin/console cache:clear --no-warmup || true

# Erases .env file to prevent symfony to crash. 
RUN rm -f /var/www/html/.env

RUN rm /etc/nginx/sites-enabled/default

COPY .docker/render/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY .docker/render/default.conf /etc/nginx/conf.d/default.conf
COPY .docker/render/supervisor.conf /etc/supervisord.conf

# Gives permissions to www-data user (symfony) to the entire folder.
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# No needed any longer.
# RUN  mkdir -p /var/www/html/var \
#    && chown -R www-data:www-data /var/www/html/var \
#    && chmod -R 755 /var/www/html/var
# RUN mkdir -p /var/www/html/var/log && chown -R www-data:www-data /var/www/html/var/log

# Expose port 80 to serve HTML (localhost)
#EXPOSE 80

# Expose port 10000 to serve HTML (render)
ENV PORT=10000
EXPOSE 10000

# Launch nginx and php together
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]


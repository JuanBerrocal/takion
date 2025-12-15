# This is a Dockerfile to be deplyed in render

# First stage
FROM php:8.4.6-fpm AS build

# Extensions needed for Symfony and composer
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libzip-dev zip libpng-dev libjpeg-dev libfreetype6-dev libicu-dev g++ \
    && docker-php-ext-install pdo pdo_pgsql zip intl mbstring

# Installs composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Copies configuration files
COPY composer.json composer.lock ./

# Installs PHP dependencies.  It doesnt execute scripts at the first stage.
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-scripts

# Copies the rest.
COPY . .

# We dont execute any Symfony code her to build Symfony cache here (NO .env)
# If so, then Symfony doenst pass the APP_ENV variable, and then symfony starts in DEV mode, and then tries to load an .env file that doesnt exits, and then crash.
#RUN touch .env && APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear --no-warmup
#RUN rm -rf var/cache/* \
#    && APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear --no-warmup \
#    && APP_ENV=prod APP_DEBUG=0 php bin/console cache:warmup


# Second stage
FROM php:8.4.6-fpm

# Extensions needed for Symfony and composer
RUN apt-get update && apt-get install -y \
    nginx supervisor git unzip curl libpq-dev libzip-dev zip libpng-dev libjpeg-dev libfreetype6-dev libicu-dev g++ \
    && docker-php-ext-install pdo pdo_pgsql zip intl mbstring

# RUN echo "listen = 127.0.0.1:9000" >> /usr/local/etc/php-fpm.d/zz-docker.conf
# RUN printf "[www]\nlisten = 127.0.0.1:9000\nclear_env = no\n" > /usr/local/etc/php-fpm.d/zz-docker.conf


RUN printf "[www]\n\
listen = 127.0.0.1:9000\n\
clear_env = no\n\
env[APP_ENV] = prod\n\
env[APP_DEBUG] = 0\n" > /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /var/www/html

# Copy app from build
COPY --from=build /app /var/www/html

# composer doesnt exists any longer at this stage, so these lines will fail.
# RUN COMPOSER_ALLOW_SUPERUSER=1 composer run-script post-install-cmd || true
# RUN php bin/console cache:clear --no-warmup || true

# Erases .env file to prevent symfony to crash. 
# RUN rm -f /var/www/html/.env


# Configuration for render/nginx/supervisor
#COPY .docker/render/www.conf /etc/php/8.4/fpm/pool.d/www.conf
COPY .docker/render/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY .docker/render/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY .docker/render/default.conf /etc/nginx/conf.d/default.conf
COPY .docker/render/supervisor.conf /etc/supervisord.conf

#Clean default nginx configuration.
RUN rm /etc/nginx/sites-enabled/default || true

# Gives permissions to www-data user (symfony) to the entire folder.
RUN mkdir -p var/cache/prod var/log \
    && chown -R www-data:www-data var/cache var/log \
    && chmod -R 775 var/cache var/log \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80 to serve HTML (localhost)
#EXPOSE 80

# Expose port 10000 to serve HTML (render)
ENV PORT=10000
EXPOSE 10000

# Launch nginx and php together
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]


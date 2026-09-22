FROM php:7.4-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    libonig-dev \
    libfreetype6-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql \
    zip \
    gd

RUN a2enmod rewrite

# Create PHP error log file with proper permissions
RUN touch /var/log/php_errors.log && chmod 666 /var/log/php_errors.log

COPY apache/vhost.conf /etc/apache2/sites-enabled/000-default.conf

COPY php/php.ini /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html
FROM php:7.1-fpm
RUN apt-get update && buildDeps="libpq-dev libzip-dev curl git wget zlib1g-dev libicu-dev netcat" && apt-get install -y $buildDeps --no-install-recommends \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip intl
RUN wget https://getcomposer.org/composer.phar && mv composer.phar /usr/bin/composer && chmod +x /usr/bin/composer


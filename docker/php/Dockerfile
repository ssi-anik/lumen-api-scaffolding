FROM php:7.4-fpm

RUN apt-get update

RUN apt-get install -y libpq-dev libpng-dev curl nano unzip zip git jq supervisor

RUN docker-php-ext-install pdo_pgsql

#RUN pecl install -o -f redis opcache
RUN pecl install -o -f redis

#RUN docker-php-ext-enable redis opcache
RUN docker-php-ext-enable redis

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY /docker/php/conf.ini /usr/local/etc/php/conf.d/custom.ini

COPY /docker/php/newrelic_installer.sh /newrelic
RUN chmod +x /newrelic

ARG ENVIRONMENT
ARG VERSION
ARG NR_LICENSE_KEY

ENV ENVIRONMENT=$ENVIRONMENT
ENV VERSION=$VERSION
ENV NR_LICENSE_KEY=$NR_LICENSE_KEY

RUN if [ "$ENVIRONMENT" = "production" ]; then /newrelic ; fi

WORKDIR /var/www/html

#COPY ./composer.* ./

#RUN composer install --no-scripts --no-interaction --no-autoloader
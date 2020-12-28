ARG PHP_IMAGE_TAG

FROM php:$PHP_IMAGE_TAG

RUN apt update && \
    apt install -y librdkafka-dev libzip-dev && \
    pecl install rdkafka && \
    docker-php-ext-enable rdkafka && \
    docker-php-ext-install zip json pcntl && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer.phar
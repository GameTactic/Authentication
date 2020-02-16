FROM php:7.4-fpm-alpine

RUN apk add --no-cache $PHPIZE_DEPS libsodium-dev \
	&& docker-php-ext-install -j8 pdo_mysql sockets sodium opcache \
    && pecl install xdebug redis \
    && docker-php-ext-enable xdebug redis \
    && apk del --purge $PHPIZE_DEPS
RUN wget https://getcomposer.org/composer-stable.phar \
    && mv composer-stable.phar composer \
    && chmod +x composer \
    && mv composer /bin
RUN apk add --no-cache fish nano redis

WORKDIR /app

FROM wordpress:php7.4

RUN yes '' | pecl install redis
RUN docker-php-ext-enable redis

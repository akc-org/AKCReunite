FROM wordpress:php8.3

RUN yes '' | pecl install redis
RUN docker-php-ext-enable redis

FROM php:7.1-cli
COPY ./ /usr/src/myapp
WORKDIR /usr/src/myapp

RUN pecl install channel://pecl.php.net/vld-0.14.0
RUN docker-php-ext-enable vld

CMD [ "php", "./example/Chat/server.php" ]
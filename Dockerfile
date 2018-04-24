FROM php:7.1-cli
COPY . /usr/src/myapp
WORKDIR /usr/src/myapp

CMD [ "php", "./example/chat/server.php" ]
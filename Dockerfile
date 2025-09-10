FROM php:8.1-apache
RUN apt-get update && apt-get install -y libssl-dev libzip-dev git unzip && docker-php-ext-install pdo pdo_mysql
# Install mongodb extension via pecl
RUN pecl install mongodb && docker-php-ext-enable mongodb
COPY public/ /var/www/html/
WORKDIR /var/www/html

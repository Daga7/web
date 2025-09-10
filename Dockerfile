FROM php:8.2-apache

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    libssl-dev pkg-config unzip git \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Copiar proyecto
COPY . /var/www/html/

# Ajustar DocumentRoot a /public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

WORKDIR /var/www/html/

# Instalar dependencias PHP
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]

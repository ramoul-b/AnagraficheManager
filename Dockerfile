FROM php:8.1-apache
COPY . /var/www/html/
WORKDIR /var/www/html
RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/html
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
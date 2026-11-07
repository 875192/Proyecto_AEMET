# Imagen de la aplicación web: Apache + PHP 8.3 con el driver MySQL (PDO).
FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql

# La raíz del servidor es la carpeta public/ (el resto del código queda fuera del alcance del navegador).
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Render par PHP + Apache chalane ke liye
FROM php:8.2-apache
RUN docker-php-ext-install pdo_mysql
RUN a2enmod rewrite \
 && sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
COPY . /var/www/html/
RUN mkdir -p /var/www/html/uploads/students && chown -R www-data:www-data /var/www/html/uploads
EXPOSE 80

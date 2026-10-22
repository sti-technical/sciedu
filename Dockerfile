# Render par PHP + Apache chalane ke liye
FROM php:8.2-apache
# zip: Excel (.xlsx) questions upload ke liye
RUN apt-get update && apt-get install -y --no-install-recommends libzip-dev \
 && docker-php-ext-install pdo_mysql zip \
 && rm -rf /var/lib/apt/lists/*
# Photo / Excel upload ki limit
RUN { echo 'upload_max_filesize=5M'; echo 'post_max_size=8M'; } > /usr/local/etc/php/conf.d/uploads.ini
RUN a2enmod rewrite \
 && sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
COPY . /var/www/html/
RUN mkdir -p /var/www/html/uploads/students && chown -R www-data:www-data /var/www/html/uploads
EXPOSE 80

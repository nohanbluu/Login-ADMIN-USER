FROM php:8.2-apache

# Fix MPM conflict - hapus semua dulu, baru enable prefork
RUN rm -f /etc/apache2/mods-enabled/mpm_*.conf \
          /etc/apache2/mods-enabled/mpm_*.load \
    && ln -s /etc/apache2/mods-available/mpm_prefork.conf \
             /etc/apache2/mods-enabled/mpm_prefork.conf \
    && ln -s /etc/apache2/mods-available/mpm_prefork.load \
             /etc/apache2/mods-enabled/mpm_prefork.load

# Install ekstensi
RUN docker-php-ext-install pdo pdo_mysql

# Enable rewrite
RUN a2enmod rewrite

# Fix ServerName warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

FROM php:8.2-apache

# Fix MPM - hapus semua, enable prefork saja
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

# Buat entrypoint langsung di dalam Dockerfile (tidak butuh file external)
RUN printf '#!/bin/bash\nset -e\nrm -f /etc/apache2/mods-enabled/mpm_*.conf /etc/apache2/mods-enabled/mpm_*.load\nln -s /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf\nln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load\nexec "$@"\n' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]

EXPOSE 80

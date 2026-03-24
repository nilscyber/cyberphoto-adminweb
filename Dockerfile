FROM php:8.3-apache

# Install PHP extensions
RUN apt-get update && apt-get install -y libpq-dev && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install mysqli pdo pdo_mysql pgsql calendar

# Enable Apache mod_rewrite and SSL
RUN a2enmod rewrite ssl

# Generate self-signed certificate for local development
RUN openssl req -x509 -nodes -days 3650 -newkey rsa:2048 \
    -keyout /etc/ssl/private/selfsigned.key \
    -out /etc/ssl/certs/selfsigned.crt \
    -subj "/CN=localhost"

# Copy custom Apache vhost config
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Add lib/ to PHP include path
RUN echo "include_path = \".:/var/www/html/lib\"" > /usr/local/etc/php/conf.d/include-path.ini && \
    echo "error_reporting = E_ERROR | E_PARSE" > /usr/local/etc/php/conf.d/error-reporting.ini

# Copy application files
COPY app/ /var/www/html/

# Set ownership
RUN chown -R www-data:www-data /var/www/html

# Config via environment variables (K8s ConfigMaps/Secrets)
# e.g. DB_HOST, DB_NAME, DB_USER, DB_PASS
# Access in PHP with getenv('DB_HOST')

EXPOSE 80 443

HEALTHCHECK --interval=30s --timeout=3s \
  CMD curl -f http://localhost/healthz.php || exit 1

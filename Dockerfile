# Use official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies for PostgreSQL and other extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (including PostgreSQL support)
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite for URL routing
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Install PHP dependencies if composer.json exists
RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi

# Set proper permissions for web server
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/data \
    && chmod -R 777 /var/www/html/logs

# Configure Apache to use Render's PORT environment variable
# Create a startup script that substitutes PORT at runtime
RUN echo '#!/bin/bash\n\
export PORT=${PORT:-80}\n\
sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf\n\
sed -i "s/*:80/*:$PORT/" /etc/apache2/sites-available/000-default.conf\n\
exec apache2-foreground' > /usr/local/bin/start-apache.sh && \
chmod +x /usr/local/bin/start-apache.sh

# Configure Apache DocumentRoot (adjust if index.php is in subdirectory)
# If your index.php is in root, this is correct
# If in 'public' folder, change to: /var/www/html/public
RUN sed -i 's|/var/www/html|/var/www/html|g' /etc/apache2/sites-available/000-default.conf

# Expose port (Render will override this with PORT env var)
EXPOSE 80

# Start Apache using startup script that handles PORT variable
CMD ["/usr/local/bin/start-apache.sh"]


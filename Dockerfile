# Use an official PHP Apache image with PHP 8.1
FROM php:8.1-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
  && docker-php-ext-install pdo pdo_mysql zip \
  && apt-get clean

# Enable Apache mod_rewrite for URL routing
RUN a2enmod rewrite

# Copy Composer from the official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set environment variable to allow Composer plugins to run as superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

# Set the working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./ 

# Install composer dependencies (if composer.json exists)
RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi

# Copy application files
COPY public/ ./public/
COPY src/ ./src/
COPY .env ./.env

# Ensure logs directory exists and has correct permissions
RUN chown -R www-data:www-data /var/www/html/src/logs && \
    chmod -R 775 /var/www/html/src/logs

# Ensure .env file is readable
RUN chmod 644 /var/www/html/.env

# Update Apache configuration to use the html folder as DocumentRoot
RUN sed -ri -e 's!/var/www/html!/var/www/html/!g' /etc/apache2/apache2.conf

# Expose port 80 for Apache
EXPOSE 80

# Run composer dump-autoload after everything is set up
RUN composer dump-autoload --optimize

# Start Apache in the foreground
CMD ["apache2-foreground"]

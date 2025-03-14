# Use an official PHP Apache image with the latest PHP version (adjust tag as needed)
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

# Copy composer files first for better caching if you are using Composer
COPY composer.json composer.lock ./

# Install composer dependencies (if composer.json exists)
RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi

# Copy the rest of the application code.
# Here, we assume your public files are in the "public" folder.
COPY public/ ./public/
COPY src/ ./src/

# Update Apache configuration to use the public folder as DocumentRoot.
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf

# Expose port 80 for Apache
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]

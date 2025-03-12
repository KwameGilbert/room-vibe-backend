# Use an official PHP Apache image with the latest PHP version (adjust tag as needed)
FROM php:8.1-apache

# Install PHP extensions required by your application (e.g., PDO and pdo_mysql)
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite for URL routing
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Copy composer files first for better caching if you are using Composer
COPY composer.json composer.lock ./

# Install composer dependencies (if vendor is not already built)
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

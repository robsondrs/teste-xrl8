# Use the official PHP 8.2 image as the base image
FROM php:8.2-apache

# Set the working directory in the container
WORKDIR /var/www/html

# Copy the composer.json and composer.lock files to the container
COPY composer*.json ./

# Install composer and project dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-scripts --no-autoloader \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# Copy the rest of the application code to the container
COPY . .

# Generate the autoloader
RUN composer dump-autoload --optimize

# Start the Apache server
CMD ["apache2-foreground"]
# Use the official PHP 8.2 image as the base image
FROM php:8.2-apache

# Set the working directory in the container
WORKDIR /var/www/html

# Install composer and project dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the composer.json and composer.lock files to the container
COPY composer*.json .
RUN composer install --no-scripts --no-autoloader

# Copy the rest of the application code to the container
COPY . .

# Generate the autoloader
RUN composer dump-autoload --optimize

# Start the Apache server
CMD ["apache2-foreground"]
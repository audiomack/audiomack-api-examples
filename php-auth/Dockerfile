# Use PHP 8.2
FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# pear/http_oauth predates PHP 8 and emits deprecation notices; keep them out of
# the response body (they break the OAuth redirect) and send errors to the logs
RUN printf 'error_reporting = E_ALL & ~E_DEPRECATED\ndisplay_errors = Off\nlog_errors = On\n' \
    > /usr/local/etc/php/conf.d/errors.ini

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install dependencies and update to avoid deprecation errors
RUN composer install && composer update

# Add the required include path to config.php
RUN echo 'set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ ."/vendor/pear/http_request2");' >> config.php

# Expose port 8080
EXPOSE 8080

# Start PHP's built-in server with explicit host binding
CMD ["php", "-S", "0.0.0.0:8080", "-t", "/var/www/html"] 

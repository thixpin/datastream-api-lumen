# Use a PHP image with PHP-FPM
FROM php:8.3-fpm

# Install additional PHP extensions and dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    cron \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install zip \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install pcntl \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*


# Copy the PHP-FPM configuration file
ADD devops/www.conf /usr/local/etc/php-fpm.d/www.conf


# Set the working directory
RUN mkdir -p /var/www/html && chown www-data:www-data /var/www/html
WORKDIR /var/www/html

# Run composer install
COPY composer.json composer.lock ./
RUN cd /var/www/html && COMPOSER_ALLOW_SUPERUSER=1 composer install --no-scripts --no-autoloader
RUN chown -R www-data:www-data /var/www/html

RUN echo "* * * * * root php /var/www/html/artisan schedule:run >> /var/log/cron.log 2>&1" >> /etc/crontab
# Create the log file to be able to run tail
RUN touch /var/log/cron.log

# Start the cron service and PHP-FPM
CMD cron && php-fpm


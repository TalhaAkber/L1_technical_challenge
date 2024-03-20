# Use the official PHP image as a base
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libpq-dev \
    cron \
    && docker-php-ext-install pdo pdo_pgsql intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Install compose packages
RUN composer install --no-scripts --no-autoloader

# Add cron job
RUN touch /var/log/symfony.log
RUN chmod 0666 /var/log/symfony.log
RUN echo "* * * * * /usr/local/bin/php /var/www/html/bin/console app:process-log >> /var/log/symfony.log 2>&1" > /etc/cron.d/process-log
RUN chmod 0644 /etc/cron.d/process-log
RUN crontab /etc/cron.d/process-log

# Copy entrypoint script
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Start the Symfony server
EXPOSE 8000
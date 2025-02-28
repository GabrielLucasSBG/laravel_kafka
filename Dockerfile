### Dockerfile
FROM php:8.4-fpm

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip \
    git \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    librdkafka-dev \
    build-essential \
    libssl-dev \
    zlib1g-dev \
    libsnappy-dev \
    git \
    cmake \
    cron \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Kafka extension with Snappy support
RUN git clone --depth 1 --branch v2.5.0 https://github.com/confluentinc/librdkafka.git /tmp/librdkafka \
    && cd /tmp/librdkafka \
    && cmake -B build -S . -DENABLE_LZ4=ON -DENABLE_SNAPPY=ON \
    && cmake --build build --target install \
    && rm -rf /tmp/librdkafka \
    && pecl install rdkafka-6.0.3 \
    && docker-php-ext-enable rdkafka

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing application directory contents
COPY . /var/www

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Add Laravel Scheduler to cron
RUN echo "* * * * * www-data php /var/www/artisan schedule:run >> /dev/null 2>&1" | tee -a /etc/cron.d/laravel-scheduler \
    && chmod 0644 /etc/cron.d/laravel-scheduler \
    && crontab /etc/cron.d/laravel-scheduler

# Start PHP-FPM and cron
CMD service cron start && php-fpm

# Expose port 9000
EXPOSE 9000

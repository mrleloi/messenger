FROM php:8.1-fpm

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    nano \
    nodejs \
    npm \
    redis-server \
    supervisor \
    unzip \
    git \
    curl \
    libonig-dev \
    libzip-dev \
    libgd-dev
# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
#Mine

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-external-gd
RUN docker-php-ext-install gd

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /var/www

# Install Supervisor for process control
RUN mkdir -p /var/log/supervisor

# Copy the Laravel worker configuration
COPY worker.conf /etc/supervisor/conf.d/

# Start Supervisor to manage the Laravel worker process
CMD ["/usr/bin/supervisord", "-n"]

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Run Composer install
RUN composer update

# Run NPM install
RUN npm install
RUN npm run prod

COPY mix-manifest.json /var/www/public/vendor/messenger/mix-manifest.json

USER root

COPY helpers.php /var/www/vendor/rtippin/messenger/src/

# CHMOD files/folders
RUN chown -R www:www storage
RUN chown -R www:www bootstrap/cache
RUN chmod -R 777 storage
RUN chmod -R 777 bootstrap/cache

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]

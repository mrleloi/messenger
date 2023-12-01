FROM php:8.1-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip
    nano \
    nodejs \
    npm \
    redis-server \
    supervisor \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libgd-dev
# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
#Mine

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip
RUN docker-php-ext-configure gd --with-external-gd
RUN docker-php-ext-install gd

# Install composer
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Add user for laravel application
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
#COPY . /var/www

# Supervisor
# Install Supervisor for process control
RUN mkdir -p /var/log/supervisor
# Copy the Laravel worker configuration
COPY worker.conf /etc/supervisor/conf.d/
# Start Supervisor to manage the Laravel worker process
CMD ["/usr/bin/supervisord", "-n"]

# Run Composer install
RUN composer install
RUN composer update

# Run NPM install
RUN npm install
RUN npm run prod

USER root
COPY mix-manifest.json /var/www/public/vendor/messenger/mix-manifest.json
COPY helpers.php /var/www/vendor/rtippin/messenger/src/helpers.php

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

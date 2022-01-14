FROM php:7.4-fpm

# Arguments defined in docker-compose.yml
ARG user=sammy
ARG uid=1000
ARG enable_xdebug='off'

# Install system dependencies
RUN apt-get update && apt-get install -y \
  git \
  curl \
  libpq-dev \
  libpng-dev \
  libonig-dev \
  libicu-dev \
  zip \
  netcat \
  unzip

# Enable Xdebug
RUN if [ "$enable_xdebug" = "on" ]; then \
  yes | pecl install xdebug && \
  echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini && \
  echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini && \
  echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini && \
  php -m; \
  else \
  echo "Skip xdebug support"; \
  fi

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd intl

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
  chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

USER $user
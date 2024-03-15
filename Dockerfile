FROM php:8.2-fpm

# Arguments used for permissions below
ARG user=employee
ARG uid=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    zip \
    unzip \
    libzip-dev \
    libz-dev \
    libbz2-dev \
    libxslt-dev \
    libgmp-dev \
    npm \
    libssh2-1-dev \
    libssh2-1 \
    libpcre3-dev \
    libyaml-dev \
    libssl-dev \
    wget \
    ssh \
    libgpgme11-dev \
    libmagickwand-dev

# Clear cache
RUN apt-get clean all && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli mbstring exif pcntl bcmath gd intl zip soap bz2 calendar gettext gmp sockets opcache

# Install redis & imagick & pcov
RUN pecl install redis imagick pcov

# Enable extensions
RUN docker-php-ext-enable pdo_mysql imagick pcov

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

USER $user

FROM wordpress:php8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    msmtp \
    msmtp-mta \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    intl \
    zip \
    opcache \
    pdo_mysql \
    exif \
    mysqli

# Enable Apache modules
RUN a2enmod rewrite headers expires

# Configure msmtp for Mailhog
RUN echo "account default" > /etc/msmtprc \
    && echo "host mailhog" >> /etc/msmtprc \
    && echo "port 1025" >> /etc/msmtprc \
    && echo "from wordpress@mydefenselaw.local" >> /etc/msmtprc \
    && chmod 644 /etc/msmtprc

# Set sendmail path to use msmtp
RUN echo "sendmail_path = /usr/bin/msmtp -t" > /usr/local/etc/php/conf.d/sendmail.ini

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Working directory
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80

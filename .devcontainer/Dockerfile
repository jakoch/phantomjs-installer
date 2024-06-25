FROM mcr.microsoft.com/devcontainers/php:8

# Setup system dependecies
RUN apt-get update && apt-get install -y \
    # for PhantomJS
    fontconfig \
    # for PHP extension: bzip
    bzip2 libbz2-dev \
    # for PHP extension: intl
    libicu-dev \
    # for PHP extension: gettext
    gettext

# Setup PHP dependencies
RUN docker-php-ext-install \
    bz2 \
    gettext \
    intl \
    pdo \
    pdo_mysql

# Update Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer self-update
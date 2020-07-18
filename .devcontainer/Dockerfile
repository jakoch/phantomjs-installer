FROM php:7.4-cli

ENV DEBIAN_FRONTEND=noninteractive

ARG USERNAME=vscode
ARG USER_UID=1000
ARG USER_GID=$USER_UID

# Configure apt and install packages
RUN apt-get update \
    && apt-get -y install --no-install-recommends apt-utils dialog 2>&1 \
    && apt-get -y install git openssh-client less iproute2 procps lsb-release unzip \
    && apt-get -y install libfontconfig1 libbz2-dev libzip-dev \
    #
    # Xdebug
    && yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    #
    # Create a non-root user to use if preferred
    && groupadd --gid $USER_GID $USERNAME \
    && useradd -s /bin/bash --uid $USER_UID --gid $USER_GID -m $USERNAME \
    # [Optional] Add sudo support for the non-root user
    && apt-get install -y sudo \
    && echo $USERNAME ALL=\(root\) NOPASSWD:ALL > /etc/sudoers.d/$USERNAME\
    && chmod 0440 /etc/sudoers.d/$USERNAME \
    #
    # Clean up
    && apt-get autoremove -y \
    && apt-get clean -y \
    && rm -rf /var/lib/apt/lists/* \
    #
    # Install Composer v1, then self-update to snapshot of v2
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer self-update --snapshot \
    && composer --version

# For PhantomJS
ENV OPENSSL_CONF=/etc/ssl/

# Install bz2, requires libbz2-dev
RUN docker-php-ext-install bz2

# Install zip, requires libzip-dev zlib1g-dev
RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip

ENV DEBIAN_FRONTEND=dialog

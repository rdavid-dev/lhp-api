FROM php:8.1.4-fpm-alpine3.14

# Arguments defined in docker-compose.yml
ARG USER
ARG UID

# Set working directory
WORKDIR /var/www/html

# Installing bash
RUN apk add bash
RUN sed -i 's/bin\/ash/bin\/bash/g' /etc/passwd

# Install dependencies
RUN set -xe \
    && apk add --no-cache \
        c-client \
        ca-certificates \
        freetds \
        freetype \
        gettext \
        gmp \
        icu-libs \
        imagemagick \
        imap \
        libffi \
        libgmpxx \
        libintl \
        libjpeg-turbo \
        libpng \
        libpq \
        libssh2 \
        libstdc++ \
        libtool \
        libxpm \
        libxslt \
        libzip \
        make \
        tidyhtml \
        tzdata \
        vips \
        yaml

#############################################
### Install and enable PHP extensions
#############################################

# Development dependencies
RUN set -xe \
    && apk add --no-cache --virtual .build-deps \
        autoconf \
        bzip2-dev \
        cmake \
        curl-dev \
        freetds-dev \
        freetype-dev \
        gcc \
        gettext-dev \
        git \
        gmp-dev \
        icu-dev \
        imagemagick-dev \
        imap-dev \
        krb5-dev \
        libc-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libssh2-dev \
        libwebp-dev \
        libsodium-dev \
        libxml2-dev \
        libxpm-dev \
        libxslt-dev \
        libzip-dev \
        openssl-dev \
        pcre-dev \
        pkgconf \
        tidyhtml-dev \
        vips-dev \
        yaml-dev \
        zlib-dev

RUN ln -s /usr/bin/php8 /usr/bin/php

# Install gd
RUN ln -s /usr/lib/x86_64-linux-gnu/libXpm.* /usr/lib/ \
    && docker-php-ext-configure gd \
        --enable-gd \
        --with-webp \
        --with-jpeg \
        --with-xpm \
        --with-freetype \
        --enable-gd-jis-conv \
    && docker-php-ext-install -j$(nproc) gd \
    && true

# Install intl
RUN docker-php-ext-install -j$(nproc) intl \
    && true

# Install mysqli
RUN docker-php-ext-install -j$(nproc) mysqli \
    && true 

# Install oauth
RUN pecl install oauth \
    && docker-php-ext-enable oauth \
    && true

# Install pdo_mysql
RUN docker-php-ext-configure pdo_mysql --with-zlib-dir=/usr \
    && docker-php-ext-install -j$(nproc) pdo_mysql \
    && true

# Installing composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN rm -rf composer-setup.php

# RUN ln -sf /dev/stdout /var/log/nginx/access.log
# RUN ln -sf /dev/stderr /var/log/nginx/error.log

RUN addgroup -g ${UID} --system $USER
RUN adduser -G $USER --system -D -s /bin/sh -u ${UID} $USER

RUN sed -i "s/user = www-data/user = $USER/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = $USER/g" /usr/local/etc/php-fpm.d/www.conf
RUN echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf

RUN mkdir -p /home/$USER/.composer && \
    chown -R $USER:$USER /home/$USER

#COPY php ini
COPY ./config/php/php.ini /usr/local/etc/php/conf.d/php.ini

EXPOSE 9000
CMD ["php-fpm"]
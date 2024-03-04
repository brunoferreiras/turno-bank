#
# PHP Base
#
FROM php:8.3-fpm-alpine3.19 as base
WORKDIR /var/www/html

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

ENV TZ=UTC

RUN apk update && \
    # Set timezone
    apk add --no-cache tzdata && \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && \
    # Add and set Group
    addgroup -g 1000 laravel && adduser -G laravel -g laravel -s /bin/sh -D laravel && \
    # Create folder to run
    mkdir -p /var/www/html && \
    # Set Profile
    chown laravel:laravel /var/www/html && \
    apk add --update --no-cache \
    curl \
    wget \
    make \
    sudo \
    git \
    bash \
    linux-headers \
    busybox-suid && \
    # Preparing and Installing PHP
    install-php-extensions \
    gd \
    xdebug \
    redis \
    opcache \
    zip \
    intl \
    pdo_mysql && \
    # Install Supervisor
    apk add supervisor && \
    # Installing Composer
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    # Installing Tini
    wget -O /tini https://github.com/krallin/tini/releases/download/v0.18.0/tini-static && \
    chmod +x /tini && \
    # Cleaning up
    rm -rf /tmp/* && \
    rm -rf /var/cache/apk/* && \
    rm -rf /usr/bin/install-php-extensions && \
    echo "laravel  ALL = ( ALL ) NOPASSWD: ALL" >> /etc/sudoers

#
# PHP Dependencies
#
FROM composer:2.5 as vendor
WORKDIR /app

COPY composer.json composer.lock /app/
COPY database/ database/
COPY tests/ tests/

ENV PATH /composer/vendor/bin:$PATH

RUN composer install \
    --optimize-autoloader \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --no-dev \
    --profile

FROM base as development
WORKDIR /var/www/html
# Environment variables
ENV PATH=/home/laravel/.composer/vendor/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
ENV APP_ENV=local

ADD .docker/php/entrypoint.sh /bin/entrypoint.sh
ADD .docker/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
ADD .docker/php/opcache.ini /usr/local/etc/php/conf.d/10-opcache.ini

USER laravel
ENTRYPOINT ["/tini", "--", "/bin/entrypoint.sh"]
CMD [ "bash" ]

FROM base as production
WORKDIR /var/www/html

USER laravel

# Environment variables
ENV PATH=/home/laravel/.composer/vendor/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
ENV APP_ENV=production

COPY --chown=laravel:laravel . /var/www/html
COPY --chown=laravel:laravel --from=vendor /app/vendor /var/www/html/vendor
COPY --chown=laravel:laravel .docker/php/supervisord.conf /etc/supervisord.conf

ADD .docker/php/entrypoint.sh /bin/entrypoint.sh
ADD .docker/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
ADD .docker/php/opcache.ini /usr/local/etc/php/conf.d/10-opcache.ini

ENTRYPOINT ["/tini", "--", "/bin/entrypoint.sh"]

CMD [ "bash" ]

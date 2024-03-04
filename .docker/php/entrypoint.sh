#!/bin/sh
set -eu

if [[ $APP_ENV = "local" ]]; then
    sudo sed -i 's/opcache.enable=1/opcache.enable=0/g' /usr/local/etc/php/conf.d/10-opcache.ini
    sudo sed -i 's/opcache.enable_cli=1/opcache.enable_cli=0/g' /usr/local/etc/php/conf.d/10-opcache.ini
    composer install
else
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

/usr/bin/supervisord --pidfile /tmp/supervisord.pid --user laravel -c /etc/supervisord.conf

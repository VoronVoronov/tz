#!/bin/sh

sleep 10

php artisan key:generate --no-interaction --force

php artisan migrate:fresh --force

php artisan passport:install --force

if [ ! -f /var/www/storage/oauth-private.key ]; then
    php artisan passport:keys --force
fi

php-fpm

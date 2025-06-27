#!/bin/sh

sleep 10

php artisan key:generate --no-interaction --force

php artisan migrate:fresh --force

php artisan passport:keys --force

php artisan passport:client --personal --name="Personal Access Client" --no-interaction
php artisan passport:client --password --name="Password Grant Client" --no-interaction

case "$*" in
    *queue:work*)
        exec "$@"
        ;;
    *)
        exec php-fpm
        ;;
esac

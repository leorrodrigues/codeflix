#!/bin/bash

#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh
npm config set cache /var/www/.npm-cache
cd /var/www/frontend
npm install
cd ..

cd backend
chown -R www-data:www-data .
if [ ! -f ".env" ]; then
    cp .env.example .env
fi
if [ ! -f ".env.testing" ]; then
    cp .env.testing.example .env.testing
fi
composer install
chmod -R 755 /var/www/storage
php artisan key:generate
php artisan migrate

php-fpm

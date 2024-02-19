#!/bin/bash

cd /home/forge/aas.svdp.org.au
git fetch --all
git reset --hard origin/master
composer install --no-interaction --prefer-dist --optimize-autoloader
echo "" | sudo -S service php7.1-fpm reload

if [ -f artisan ]
then
    php artisan migrate --force
fi

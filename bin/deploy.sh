#!/bin/bash

time_start="$(date +%s)"
time_now="$(date +%Y-%m-%d-%H-%M-%S)"

echo "==> Starting deployment..."
echo "==> Updating app..."

rsync \
-ahzP \
--exclude '.git/' \
--exclude '.env' \
--exclude '.env.example' \
--exclude '.eslintrc' \
--exclude '.gitattributes' \
--exclude '.gitignore' \
--exclude 'gulpfile.js' \
--exclude 'package.json' \
--exclude 'phpunit.xml' \
--exclude 'bin/' \
--exclude 'node_modules/' \
--exclude 'public/storage' \
--exclude 'tests/' \
--exclude 'storage/app/' \
--exclude 'storage/framework/' \
--exclude 'storage/logs/' \
--exclude 'yarn.lock' \
--delete \
/srv/www/aas/ \
osky@osky.webfactional.com:/home/osky/webapps/vinnies_aas_src

time_end="$(date +%s)"

echo "==> Completed in "$(expr $time_end - $time_start)" seconds"

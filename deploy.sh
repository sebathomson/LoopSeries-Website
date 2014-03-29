php composer.phar update
php app/console assetic:dump
php app/console assets:install --symlink
php app/console doctrine:schema:update --force
exit 0

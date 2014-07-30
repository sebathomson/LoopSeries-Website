php composer.phar update
php app/console cache:clear --env=prod
php app/console cache:clear --env=dev
php app/console assetic:dump
php app/console assets:install --symlink
php app/console doctrine:schema:update --force
exit 0

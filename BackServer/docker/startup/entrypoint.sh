/usr/local/sbin/php-fpm -D && cron -f && tail -f /var/log/cron.log && php /application/FrontServer/bin/console app:execute-post

FROM webdevops/php-nginx:8.1-alpine
ENV WEB_DOCUMENT_ROOT=/app/public
ENV PHP_DISMOD=bz2,calendar,exiif,ffi,intl,gettext,ldap,mysqli,imap,pdo_pgsql,pgsql,soap,sockets,sysvmsg,sysvsm,sysvshm,shmop,xsl,apcu,vips,yaml,imagick,mongodb,amqp
COPY docker/php-nginx /opt/docker
WORKDIR /app
COPY . .
RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN php artisan storage:link 
# Ensure all of our files are owned by the same user and group.
RUN chown -R application:application .

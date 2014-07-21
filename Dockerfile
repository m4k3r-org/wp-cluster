##
# DiscoDonniePresents.com Storage Container
#
# $ make build
# $ make release
#
# @ver 0.2.1
##
FROM          usabilitydynamics/centos:latest
MAINTAINER    UsabilityDynamics, Inc. <info@usabilitydynamics.com>
USER          root

RUN           mkdir -p /var/www

ADD           application       /var/www/application
ADD           vendor            /var/www/vendor
ADD           storage           /var/www/storage
ADD           .htaccess         /var/www/.htaccess
ADD           wp-cli.yml        /var/www/wp-cli.yml
ADD           index.php         /var/www/index.php
ADD           sunrise.php       /var/www/sunrise.php
ADD           db.php            /var/www/db.php

VOLUME        /var/www
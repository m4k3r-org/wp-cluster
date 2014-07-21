##
# DiscoDonniePresents.com Storage Container
#
# @ver 0.2.1
##
FROM          usabilitydynamics/centos:latest
MAINTAINER    UsabilityDynamics, Inc. <info@usabilitydynamics.com>
USER          root

RUN           mkdir -p /var/www

# COPY          application       /var/www/application
# COPY          vendor            /var/www/vendor
# COPY          storage           /var/www/storage
# COPY          .htaccess         /var/www/.htaccess
# COPY          wp-cli.yml        /var/www/wp-cli.yml
# COPY          index.php         /var/www/index.php
# COPY          sunrise.php       /var/www/sunrise.php
# COPY          db.php            /var/www/db.php

ADD           . /var/www

VOLUME        /var/www

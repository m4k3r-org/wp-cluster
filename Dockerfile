#################################################################
## DiscoDonniePresents.com Application Container
##
## * Select paths are exposed that are safe to be mounted for developemnt purposes.
## * Only essential files and directories added to container that allow Grunt tasks and web-based file serving.
##
##
## @ver 0.2.1
## @author potanin@UD
##
#################################################################

FROM          usabilitydynamics/centos:latest
MAINTAINER    UsabilityDynamics, Inc. <info@usabilitydynamics.com>
USER          root

RUN           /usr/bin/npm install --global --link grunt
RUN           /usr/bin/npm install --global --link mocha

RUN           mkdir -p /var/www
RUN           mkdir -p /var/logs
RUN           mkdir -p /var/storage
RUN           mkdir -p /tmp

ENV           PHP_ENV           development
ENV           NODE_ENV          development

ADD           application           /var/www/application
ADD           vendor                /var/www/vendor
ADD           storage               /var/www/storage
ADD           .htaccess             /var/www/.htaccess
ADD           wp-cli.yml            /var/www/wp-cli.yml
ADD           index.php             /var/www/index.php
ADD           package.json          /var/www/package.json
ADD           composer.json         /var/www/composer.json
ADD           gruntfile.js          /var/www/gruntfile.js
ADD           sunrise.php           /var/www/sunrise.php
ADD           db.php                /var/www/db.php

VOLUME        /var/www/storage/public
VOLUME        /var/www/vendor/themes
VOLUME        /var/www/vendor/plugins
VOLUME        /var/www/vendor/modules
VOLUME        /var/www/vendor/libraries
VOLUME        /var/www/vendor
VOLUME        /var/www/application
VOLUME        /var/www/application/bin
VOLUME        /var/www/application/tests
VOLUME        /var/www/application/logs
VOLUME        /var/www/application/tasks
VOLUME        /var/www/application/static/etc
VOLUME        /var/www/application/static/scripts
VOLUME        /var/www/application/static/styles
VOLUME        /var/www/application/static/templates
VOLUME        /var/www/application/static/fixtures
VOLUME        /var/www

EXPOSE        80
EXPOSE        443
EXPOSE        22

WORKDIR       /var/www
CMD           --help
ENTRYPOINT    grunt
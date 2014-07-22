#################################################################
## DiscoDonniePresents.com Application Container
##
## * Select paths are exposed that are safe to be mounted for developemnt purposes.
## * Only essential files and directories added to container that allow Grunt tasks and web-based file serving.
##
## @ver 0.2.1
## @author potanin@UD
#################################################################

FROM          wpCloud/BlackBox:latest
MAINTAINER    UsabilityDynamics, Inc. <info@usabilitydynamics.com>
USER          root

RUN           mkdir -p              /var/storage
RUN           mkdir -p              /etc/ssh
RUN           mkdir -p              /etc/ssl

ENV           PHP_ENV               development
ENV           NODE_ENV              development

ADD           application           /var/www/application
ADD           vendor/libraries      /var/www/vendor/libraries
ADD           vendor/modules        /var/www/vendor/modules
ADD           vendor/plugins        /var/www/vendor/plugins
ADD           vendor/themes         /var/www/vendor/themes
ADD           node_modules          /var/www/node_modules
ADD           storage               /var/www/storage
ADD           .htaccess             /var/www/.htaccess
ADD           index.php             /var/www/index.php
ADD           package.json          /var/www/package.json
ADD           composer.json         /var/www/composer.json
ADD           gruntfile.js          /var/www/gruntfile.js
ADD           sunrise.php           /var/www/sunrise.php
ADD           db.php                /var/www/db.php

VOLUME        [ "/var/www/application" ]
VOLUME        [ "/var/www/vendor/libraries" ]
VOLUME        [ "/var/www/vendor/modules" ]
VOLUME        [ "/var/www/vendor/plugins" ]
VOLUME        [ "/var/www/vendor/themes" ]
VOLUME        [ "/var/www/node_modules" ]
VOLUME        [ "/var/www/storage" ]
VOLUME        [ "/var/www" ]

EXPOSE        80
EXPOSE        443
EXPOSE        22

WORKDIR       /var/www
CMD           [ "grunt", "--help" ]
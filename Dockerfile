##
# DiscoDonniePresents.com Storage Container
#
# $ make build
# $ make release
#
# @ver 0.0.1
##
FROM          usabilitydynamics/centos:latest
MAINTAINER    UsabilityDynamics, Inc. <info@usabilitydynamics.com>
USER          root

RUN           mkdir -p /var/www

ADD           application   /var/www/application
ADD           storage       /var/www/storage
ADD           vendor        /var/www/vendor

VOLUME        /var/www
VOLUME        /var/www/storage
VOLUME        /var/www/data

EXPOSE        22
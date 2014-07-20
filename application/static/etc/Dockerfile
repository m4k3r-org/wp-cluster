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
#RUN           git clone git@github.com:DiscoDonniePresents/www.discodonniepresents.com.git /var/www
#RUN           npm install
#RUN           composer install
#RUN           grunt install --environment=cluster

VOLUME        /var/www
VOLUME        /var/www/storage
VOLUME        /var/www/data

EXPOSE        22
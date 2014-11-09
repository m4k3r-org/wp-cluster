#################################################################
## DiscoDonniePresents.com Application Container
##
## * Select paths are exposed that are safe to be mounted for developemnt purposes.
## * Only essential files and directories added to container that allow Grunt tasks and web-based file serving.
## * Sunrise, Advanced Cache, etc. are now copied from wp-veener directory on build.
## * 495XX port range is allocated to DDP for static binding.
##
## This will create a baseline build using usabilitydynamics/blackbox:1.1.1.
## In practice, it may be prucent to utilize discodonniepresents/www.discodonniepresents.com:latest as source to ensure continuity.
##
## @ver 0.2.1
## @author potanin@UD
#################################################################

FROM          hipstack/wordpress
MAINTAINER    UsabilityDynamics, Inc.   <info@usabilitydynamics.com>
USER          root

##
## We are running a basic PHP application served by Apache, via HAproxy.
## It may make sense to move codebase into /home/web later to better organize other apps, which may need to be in their own user/home directories.
##
ADD           /                                             /var/www

## All WordPress configuration that is environment-specific should be here.
##
##
ENV           WP_CLI_CONFIG_PATH                                      /var/www/application/static/etc/wp-cli.yaml
ENV           WP_AUTH_KEY                                             i%HNa^lg#_R-0.6i7AG0cOxFGk]{Q5lBHnVz;NG$iz&#Z3XZ)[[K5ZGEo~R:*Um_
ENV           AWS_ACCESS_KEY_ID                                       AKIAJCDAT2T7FESLH3IQ
ENV           AWS_SECRET_ACCESS_KEY                                   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
ENV           AWS_STORAGE_BUCKET                                      storage.discodonniepresents.com
ENV           IMAGE_NAME                                              DiscoDonniePresents/www.discodonniepresents.com
ENV           DEPLOYMENT_VERSION                                      2.0.1
ENV           REPOSITORY_AUTH                                         8282b219ff377f9e209463564800879d7651b475
ENV           CONTENT_ORIGIN                                          http://216.22.20.143
ENV           STAGING_URL                                             http://208.52.164.220
ENV           DATA_ORIGIN                                             http://216.22.20.143
ENV           DATA_STAGING                                            http://208.52.164.220
ENV           DATA_AUTHORITY                                          http://10.88.135.8
ENV           DOCKER_REGISTRY                                         http://registry.wpcloud.io
ENV           COMPOSER_REPOSITORY                                     http://repository.usabilitydynamics.com

##
## Port 22, 8080 and 8443 are also available, but should be requested only when needed.
##
##
EXPOSE        80

##
## - Logs need to persist and do not need to be committed
## - Storage Media should typically be stored on a host machine. For local development an SSHFS mount may be created.
## - Cache is ephemeral.
##
VOLUME        [ "/var/www/wp-content/.logs" ]
VOLUME        [ "/var/www/wp-content/storage" ]
VOLUME        [ "/var/www/wp-conten/cache" ]

CMD           [ "/usr/bin/supervisord", "-n" ]
#################################################################
## DiscoDonniePresents.com Application Container
##
## * Select paths are exposed that are safe to be mounted for developemnt purposes.
## * Only essential files and directories added to container that allow Grunt tasks and web-based file serving.
## * Sunrise, Advanced Cache, etc. are now copied from wp-veener directory on build.
##
##
## @ver 0.2.1
## @author potanin@UD
#################################################################

FROM          usabilitydynamics/blackbox:1.0.0
MAINTAINER    UsabilityDynamics, Inc.   <info@usabilitydynamics.com>

ENV           WP_CLI_CONFIG_PATH        /var/www/application/static/etc/wp-cli.yml
ENV           AWS_ACCESS_KEY_ID         AKIAJCDAT2T7FESLH3IQ
ENV           AWS_SECRET_ACCESS_KEY     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
ENV           AWS_STORAGE_BUCKET        storage.discodonniepresents.com
ENV           IMAGE_NAME                DiscoDonniePresents/www.discodonniepresents.com
ENV           WP_AUTH_KEY               i%HNa^lg#_R-0.6i7AG0cOxFGk]{Q5lBHnVz;NG$iz&#Z3XZ)[[K5ZGEo~R:*Um_
ENV           DEPLOYMENT_VERSION        2.0.1
ENV           REPOSITORY_AUTH           8282b219ff377f9e209463564800879d7651b475
ENV           CONTENT_ORIGIN            http://216.22.20.143
ENV           STAGING_URL               http://208.52.164.220
ENV           DATA_ORIGIN               http://216.22.20.143
ENV           DATA_STAGING              http://208.52.164.220
ENV           DATA_AUTHORITY            http://10.88.135.8

ADD           application               /var/www/application
ADD           vendor/libraries          /var/www/vendor/libraries
ADD           vendor/modules            /var/www/vendor/modules
ADD           vendor/plugins            /var/www/vendor/plugins
ADD           vendor/themes             /var/www/vendor/themes
ADD           storage                   /var/www/storage
ADD           index.php                 /var/www/index.php
ADD           composer.json             /var/www/composer.json

ADD           vendor/modules/wp-cluster/lib/class-database.php        /var/www/db.php
ADD           vendor/modules/wp-cluster/lib/class-sunrise.php         /var/www/sunrise.php
ADD           vendor/modules/wp-veneer/lib/class-advanced-cache.php   /var/www/advanced-cache.php
ADD           vendor/modules/wp-veneer/lib/class-object-cache.php     /var/www/advaobjectnced-cache.php
ADD           vendor/modules/wp-veneer/lib/local/.htaccess            /var/www/.htaccess

COPY          application/static/ssl                                  /etc/ssl
COPY          application/static/etc/wp-cli.yaml                      /root

VOLUME        [ "/var/www/application" ]
VOLUME        [ "/var/www/vendor" ]
VOLUME        [ "/var/www/vendor/themes" ]
VOLUME        [ "/var/www/vendor/plugins" ]
VOLUME        [ "/var/www/vendor/modules" ]
VOLUME        [ "/var/www/vendor/libraries" ]
VOLUME        [ "/var/www/storage" ]
VOLUME        [ "/etc/ssl" ]

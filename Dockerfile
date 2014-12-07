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

FROM          wpcloud/site:latest
MAINTAINER    UsabilityDynamics, Inc.   <info@usabilitydynamics.com>

RUN           rm -rf /var/www/**

ADD           ./  /var/www/

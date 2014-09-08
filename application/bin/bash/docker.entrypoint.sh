#!/bin/bash
############################################################
##
## * SupervisorD is ran as root otherwise won't be able to bind to port 80.
##
## /var/www/application/bin/bash/docker.entrypoint.sh
##
############################################################

## No Argumens, start service and bash.
if [ "$*" == "" ] || [ ${1} == "/bin/bash" ]; then

  if [ -f "/usr/bin/supervisord" ]; then
    echo " - Starting Supervisor Service."
    supervisord -c /etc/supervisor/supervisord.conf -u root
  else
    echo " - Unable to start Supervisor, binary missing."
  fi

fi

## Pipe/Follow-through other commands.
exec "$@"

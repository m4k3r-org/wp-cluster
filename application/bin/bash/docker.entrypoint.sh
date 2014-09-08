#!/bin/bash
############################################################
##
## * SupervisorD is ran as root otherwise won't be able to bind to port 80.
##
## /var/www/application/bin/bash/docker.entrypoint.sh
##
############################################################

rm -rf /var/run/apache2.pid
rm -rf /var/run/apache2/apache2.pid

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

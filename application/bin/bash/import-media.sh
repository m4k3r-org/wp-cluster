#!/bin/sh

rsync -rlptDhv --rsh=ssh root@neo:/home/edm/public_html/ /var/www

rsync -rlptDhv --rsh=ssh root@lafonda.cpanel:/home/edm/storage/public/ /var/storage

rsync -rlptDhv --rsh=ssh root@neo:/home/edm/storage/ /var/storage



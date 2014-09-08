### Environment Setup
You should never have to pull this repository unless you're planning on making changes to the core image.
In most cases you want to pull the Docker Staging/Production/Latest image to setup your environment.

`docker run -tdP discodonniepresents/www.discodonniepresents.com`

The above will pull the "latest" tag, which should be very similar to what is on production. To pull the staging image:
`docker run -tdP discodonniepresents/www.discodonniepresents.com:staging`

### Production Deployment
On production, to start a daemonized container, run the following command.

```
docker run -dit \
  --privileged \
  --name=ddp.production \
  --hostname=www.discodonniepresents.com \
  -v /storage/storage.discodonniepresents.com:/var/storage \
  -v /root/.ssh:/root/.ssh \
  -v /home/core/share/www.discodonniepresents.com/logs:/var/www/application/logs \
  --publish=22 \
  --publish=80 \
  -e DB_PREFIX=edm_ \
  -e DB_NAME=edm_cluster \
  -e DB_USER=edm_cluster \
  -e DB_PASSWORD=Gbq@anViLNsa \
  -e DB_HOST=10.88.135.7 \
  -e WP_VENEER_STORAGE=static/storage \
  -e WP_BASE_DOMAIN=edm.cluster.veneer.io \
  -e HOME=/root \
  -e WP_ENV=production \
  -e PHP_ENV=production \
  -e NODE_ENV=production \
  discodonniepresents/www.discodonniepresents.com \
  /usr/bin/startServices
```

This command assumes that "storage" must reside on the host machine in /media/storage.discodonniepresents.com.

### Composer Configuration
Composer is the authority on dependency management for latest-related services. NPM is also used, but almost entirely for development tools.

* Unlike before, composer.json does not require plugins and themes. Plugins and themes are installed by WordPress, not composer.
* That being said, composer.json can require plugins absoltely essential for operation, such as WP-Veneer or for a multisite network WP-Network.
* As a rule of thumb, Composer is used to manage "must-use", and above, level dependencies. Anything below should be controlled by WordPress state.

### Image Commands
Aside from /bin/bash and /bin/supervisord commands there are several helper commands.

* /usr/bin/startServices
* /usr/bin/stopServices

* /home/blackbox/util/createDB
* /home/blackbox/util/createMySQLAdminUser
* /home/blackbox/util/importSQL
* /home/blackbox/util/runMySQL
* /home/blackbox/util/startApache2
* /home/blackbox//utilstartMySQL

### Application Structure
There is a change to the way "storage" works.

* /storage/assets - Domain-specific asset files, such as styles and scripts, that are generated based on domain settings.
* /storage/media  - Uploads.
* /storage/static - Any static files that are served before WordPress by .htaccess.

### GitHub vs Docker

* Things that should not be stored in GitHub repository: w3tc-config, plugins, themes, storage, node_modules, vendor, cache
* Things that should be stored in Docker Image: w3tc-config, plugins, themes, storage, node_modules, vendor. In case of DDP storage/media will not be stored on host due to its size.

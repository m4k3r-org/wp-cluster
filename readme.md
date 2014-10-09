### CLI Commands

Create Backup:
```
s3cmd put --no-check-md5 --reduced-redundancy edm_cluster_new.sql.tgz s3://rds.uds.io/DiscoDonniePresents/www.discodonniepresents.com/edm_cluster_new.sql.tgz
```

### GitHub Plugin Install
```
wget --quiet --header "Authorization: token ${GITHUB_UPDATER_TOKEN}" -O /tmp/wp-pagespeed.zip https://api.github.com/repos/UsabilityDynamics/wp-pagespeed/zipball/master
wget --quiet --header "Authorization: token ${GITHUB_UPDATER_TOKEN}" -O /tmp/wp-revisr.zip https://api.github.com/repos/UsabilityDynamics/wp-revisr/zipball/master
wget --quiet --header "Authorization: token ${GITHUB_UPDATER_TOKEN}" -O /tmp/wp-github-updater.zip https://api.github.com/repos/UsabilityDynamics/wp-github-updater/zipball/master

wp plugin install /tmp/wp-pagespeed.zip --force
wp plugin install /tmp/wp-revisr.zip --force
wp plugin install /tmp/wp-github-updater.zip --force
```

### Environment Setup
You should never have to pull this repository unless you're planning on making changes to the core image.
In most cases you want to pull the Docker Staging/Production/Latest image to setup your environment.

`docker run -tdP discodonniepresents/www.discodonniepresents.com`

The above will pull the "latest" tag, which should be very similar to what is on production. To pull the staging image:
`docker run -tdP discodonniepresents/www.discodonniepresents.com:staging`

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

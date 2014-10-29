### CLI Commands

Create Backup, either run "make snapshot" to create an automatic snapshot that uses branch name, or create a manually DB backup:
```
wp transient delete-all && wp cache flush
wp db export edm_production.sql
tar cvzf edm_production.sql.tgz edm_production.sql
s3cmd put --no-check-md5 --reduced-redundancy edm_production.sql.tgz s3://rds.uds.io/DiscoDonniePresents/www.discodonniepresents.com/edm_production.sql.tgz
rm -rf edm_production.sql**
```

To fetch backup locally and import it:
```
s3cmd get s3://rds.uds.io/DiscoDonniePresents/www.discodonniepresents.com/edm_production.sql.tgz
tar xvf edm_production.sql.tgz
wp db import edm_production.sql
```

### Subtrees
Add "subtree helpers" to your bash profile. (https://gist.github.com/andypotanin/e54a7322da3fa33ada7e) to simplify subtree adding/pulling/pushing:
If themes are installed using composer (e.g. composer update --prefer-source --dev) then they may be converted into non-git submodules like so:
```
makeSubtree UsabilityDynamics/wp-veneer           vendor/plugins/wp-veneer
makeSubtree UsabilityDynamics/wp-cluster          vendor/plugins/wp-cluster
makeSubtree UsabilityDynamics/wp-elastic          vendor/plugins/wp-elastic
makeSubtree UsabilityDynamics/wp-network          vendor/plugins/wp-network
makeSubtree wpCloud/wp-vertical-edm               vendor/plugins/wp-vertical-edm
makeSubtree wpCloud/wp-event-post-type            vendor/plugins/wp-event-post-type
```

```
makeSubtree DiscoDonniePresents/wp-festival       vendor/themes/wp-festival v1.0
makeSubtree DiscoDonniePresents/wp-festival       vendor/themes/wp-festival-2 v2.0
makeSubtree DiscoDonniePresents/wp-spectacle      vendor/themes/wp-spectacle v1.0
makeSubtree DiscoDonniePresents/wp-spectacle      vendor/themes/wp-spectacle-2 v2.0
makeSubtree DiscoDonniePresents/wp-spectacle-mbp  vendor/themes/wp-spectacle-mbp
makeSubtree DiscoDonniePresents/wp-spectacle-fbt  vendor/themes/wp-spectacle-fbt
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

### Staging

* Now, when commiting to the 'develop' branch, your changes will be automatically deployed to the following domain name:
  {domain}.drop.ud-dev.com, i.e. dayafter.com becomes "dayafter-com.drop.ud-dev.com"
* In addition, we have a database backup done daily, that can be restored by including the following text in your commit message:
  [drop refreshdb]

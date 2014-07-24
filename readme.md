### Environment Setup
You should never have to pull this repository unless you're planning on making changes to the core image.
In most cases you want to pull the Docker Staging/Production/Latest image to setup your environment.

`docker run -tdP discodonniepresents/www.discodonniepresents.com`

### Modular Development
To start container and expose wp-festival and wp-veneer for development, run the following.

```sh
docker run -tdP \
  -v ~/dev/wp-festival:/var/www/vendor/themes/wp-festival \
  -v ~/dev/wp-veneer:/var/www/vendor/modules/wp-veneer \
  discodonniepresents/www.discodonniepresents.com \
  /usr/bin/supervisord -n
```

This will mount the ~/dev/wp-festival and ~/dev/wp-veneer directories on your host machine.
If those directories don't exist they will be created.
You will need to run "git clone" within those directories to begin development.

### Container Development

Clone GitHub Project.
`composer create-project discodonniepresents/www.discodonniepresents.com`

Run Temporary Environment
`docker pull discodonniepresents/www.discodonniepresents.com`
`docker run -tiP --rm --name=ddp.dev --privileged discodonniepresents/www.discodonniepresents.com /bin/bash`
`docker commit --message="wip" ddp.dev discodonniepresents/www.discodonniepresents.com`
`docker push discodonniepresents/www.discodonniepresents.com`

Create Distribution
`docker save discodonniepresents/www.discodonniepresents.com > discodonniepresents/www.discodonniepresents.com.tgz`

### Production Deployment
On production, to start a daemonized container, run:
`docker run -tdP --privileged discodonniepresents/www.discodonniepresents.com`

If default configuration must be overwritten, you may pass in a configuraiton file. (not implemented)
`docker run -itP --privileged discodonniepresents/www.discodonniepresents.com start --config=https://gist.githubusercontent.com/andypotanin/848b80809dc13d16fc04/raw/`

### Composer Configuration
Composer is the authority on dependency management for latest-related services. NPM is also used, but almost entirely for development tools.

* Unlike before, composer.json does not require plugins and themes. Plugins and themes are installed by WordPress, not composer.
* That being said, composer.json can require plugins absoltely essential for operation, such as WP-Veneer or for a multisite network WP-Network.
* As a rule of thumb, Composer is used to manage "must-use", and above, level dependencies. Anything below should be controlled by WordPress state.

### Application Structure
There is a change to the way "storage" works.

* /storage/assets - Domain-specific asset files, such as styles and scripts, that are generated based on domain settings.
* /storage/media  - Uploads.
* /storage/static - Any static files that are served before WordPress by .htaccess.

### GitHub vs Docker

* Things that should not be stored in GitHub repository: w3tc-config, plugins, themes, storage, node_modules, vendor, cache
* Things that should be stored in Docker Image: w3tc-config, plugins, themes, storage, node_modules, vendor. In case of DDP storage/media will not be stored on host due to its size.

### Setting up Local Environment
Before running the following commands, please setup a new MySQL database calling it "edm_develop".
You will also need to have [gsutil](https://cloud.google.com/storage/docs/gsutil), [wp-cli](http://wp-cli.org/gsutil) and [direnv](https://github.com/zimbatm/direnv) installed:

Assuming your local site setup will be in ~/Sites/discodonniepresents.com using the **develop** branch:
```
cd ~/Sites/discodonniepresents.com
git clone git@github.com:DiscoDonniePresents/www.discodonniepresents.com.git .
git checkout develop
echo "export WP_ENV=develop" >> .envrc
echo "export DB_NAME=edm_develop" >> .envrc
echo "export DB_USER=root" >> .envrc
echo "export DB_PASSWORD=root" >> .envrc
echo "export DB_HOST=discodonniepresents.com" >> .envrc
echo "export WP_ELASTIC_SECRET_KEY=jqnp-krmw-nmap-idpk:julw-urbp-vzst-jwwv" >> .envrc
echo "export WP_ELASTIC_PUBLIC_KEY=jqnp-krmw-nmap-idpk-ooau-bkfm-bghf-jatg" >> .envrc
echo "export WP_ELASTIC_SERVICE_URL=api.discodonniepresents-com.drop.ud-dev.com" >> .envrc
echo "export WP_ELASTIC_SERVICE_INDEX=jqnp-krmw-nmap-idpk" >> .envrc
make setEnvironment
make snapshotImport
wp cloud sites
```
The very last command is to help you determine if wp-cli can connect to DB, meaning everything is setup good.

Now we will need to configure Apache. First create a "discodonniepresents.com" host then set the following environment variables:

```
SetEnv DB_NAME edm_develop
SetEnv DB_USER root
SetEnv DB_PASSWORD root
SetEnv DB_HOST localhost
SetEnv WP_ENV develop
SetEnv WP_DEBUG 1
SetEnv WP_ELASTIC_SECRET_KEY jqnp-krmw-nmap-idpk:julw-urbp-vzst-jwwv
SetEnv WP_ELASTIC_PUBLIC_KEY jqnp-krmw-nmap-idpk-ooau-bkfm-bghf-jatg
SetEnv WP_ELASTIC_SERVICE_URL api.discodonniepresents-com.drop.ud-dev.com
SetEnv WP_ELASTIC_SERVICE_INDEX jqnp-krmw-nmap-idpk
SetEnv QM_DISABLED false
```

That should be enough to get discodonniepresents.com to work locally, other network sites will also need aliases.

### Subtrees
Add "subtree helpers" to your bash profile. (https://gist.github.com/andypotanin/e54a7322da3fa33ada7e) to simplify subtree adding/pulling/pushing:

#### Pull Subtree Changes

```
@git subtree pull --prefix=wp-content/plugins/wp-amd git@github.com:UsabilityDynamics/wp-amd
@git subtree pull --prefix=wp-content/plugins/wp-cluster git@github.com:UsabilityDynamics/wp-cluster                  
@git subtree pull --prefix=wp-content/plugins/wp-crm git@github.com:UsabilityDynamics/wp-crm                      
@git subtree pull --prefix=wp-content/plugins/wp-github-updater git@github.com:UsabilityDynamics/wp-github-updater           
@git subtree pull --prefix=wp-content/plugins/wp-network git@github.com:UsabilityDynamics/wp-network                  
@git subtree pull --prefix=wp-content/plugins/wp-pagespeed git@github.com:UsabilityDynamics/wp-pagespeed                
@git subtree pull --prefix=wp-content/plugins/wp-social-stream git@github.com:UsabilityDynamics/wp-social-stream            
@git subtree pull --prefix=wp-content/themes/wp-splash-v1.0 git@github.com:UsabilityDynamics/wp-splash v1.0                    
@git subtree pull --prefix=wp-content/themes/wp-splash-v2.0 git@github.com:UsabilityDynamics/wp-splash v2.0                    
@git subtree pull --prefix=wp-content/plugins/wp-veneer git@github.com:UsabilityDynamics/wp-veneer                   
@git subtree pull --prefix=wp-content/plugins/wp-vertical-edm git@github.com:wpCloud/wp-vertical-edm                       
@git subtree pull --prefix=wp-content/themes/wp-disco-v1.0 git@github.com:DiscoDonniePresents/wp-disco v1.0
@git subtree pull --prefix=wp-content/themes/wp-disco-v2.0 git@github.com:DiscoDonniePresents/wp-disco v2.0
@git subtree pull --prefix=wp-content/themes/wp-spectacle-v1.0 git@github.com:DiscoDonniePresents/wp-spectacle v1.0
@git subtree pull --prefix=wp-content/themes/wp-spectacle-v2.0 git@github.com:DiscoDonniePresents/wp-spectacle v2.0
@git subtree pull --prefix=wp-content/themes/wp-festival-v1.0 git@github.com:DiscoDonniePresents/wp-festival v1.0
@git subtree pull --prefix=wp-content/themes/wp-festival-v2.0 git@github.com:DiscoDonniePresents/wp-festival v2.0
@git subtree pull --prefix=wp-content/themes/wp-spectacle-chmf git@github.com:DiscoDonniePresents/wp-spectacle-chmf         
@git subtree pull --prefix=wp-content/themes/wp-spectacle-mbp git@github.com:DiscoDonniePresents/wp-spectacle-mbp          
@git subtree pull --prefix=wp-content/themes/wp-spectacle-fbt git@github.com:DiscoDonniePresents/wp-spectacle-fbt          
@git subtree pull --prefix=wp-content/themes/wp-spectacle-isladelsol git@github.com:DiscoDonniePresents/wp-spectacle-isladelsol   
```

#### Update Subtrees Dependencies
```
git subtree push --prefix=wp-content/themes/wp-splash-v1.0 git@github.com:UsabilityDynamics/wp-splash v1.0
```

Add Subtree for new dependency.
```
git subtree add --prefix=wp-content/themes/wp-splash-v1.0 git@github.com:UsabilityDynamics/wp-splash v1.0
```

Show installed libs. This will only work if there is a composer.lock file.
```
composer show --installed --path
```

Show versions of libs:
```
composer show --self
```

### Cache Purging
To purge Varnish cache, run the following commands. Be advised, Varnish will only accept purge notifications from accepted IP addresses.

```
curl -X PURGE discodonniepresents.com
curl -X PURGE dayafter.com
curl -X PURGE umesouthpadre.com
```

Otherwise you may simply `make varnishPurge`.

### Staging

* Now, when commiting to the 'develop' branch, your changes will be automatically deployed to the following domain name:
  {domain}.drop.ud-dev.com, i.e. dayafter.com becomes "dayafter-com.drop.ud-dev.com"
* In addition, we have a database backup done daily, that can be restored by including the following text in your commit message:
  [drop refreshdb]

### Setting Up wpCloud.io Remote
You will need to have a wpCloud.io SSH key setup first.

Add wpCloud.io as a remote:
```
git remote add cloud git@wpcloud.io:DiscoDonniePresents/www.discodonniepresents.com.git
```

Merge any updates from production into current branch:
```
git fetch cloud
git merge cloud/production
```

Push to production deployment:
```
git push cloud production
```

### ElasticSearch Access Keys

#### Production
* Index: qccj-nxwm-etsk-niuu
* Admin Key: qccj-nxwm-etsk-niuu:chdq-tvek-desl-izlf
* Public Key: qccj-nxwm-etsk-niuu-xctg-ezsd-uixa-jhty

#### Development
* Index: jqnp-krmw-nmap-idpk
* Admin Key: jqnp-krmw-nmap-idpk:julw-urbp-vzst-jwwv
* Public Key: jqnp-krmw-nmap-idpk-ooau-bkfm-bghf-jatg

```
SetEnv WP_ELASTIC_SECRET_KEY qccj-nxwm-etsk-niuu:chdq-tvek-desl-izlf
SetEnv WP_ELASTIC_PUBLIC_KEY qccj-nxwm-etsk-niuu-xctg-ezsd-uixa-jhty
SetEnv WP_ELASTIC_SERVICE_URL api.discodonniepresents.com/documents/v1/
SetEnv WP_ELASTIC_SERVICE_INDEX qccj-nxwm-etsk-niuu
```
### Media Sync

#### Standard Sites' Media

```
gsutil -m cp -a public-read -rn /var/storage/dayafter.com/media/                         gs://media.dayafter.com/
gsutil -m cp -a public-read -rn /var/storage/beachblanketfestival.com/media/             gs://media.beachblanketfestival.com/
gsutil -m cp -a public-read -rn /var/storage/cominghomemusicfestival.com/media/          gs://media.cominghomemusicfestival.com/
gsutil -m cp -a public-read -rn /var/storage/discodonniepresents.com/media/              gs://media.discodonniepresents.com/
gsutil -m cp -a public-read -rn /var/storage/freaksbeatstreats.com/media/                gs://media.freaksbeatstreats.com/
gsutil -m cp -a public-read -rn /var/storage/gifttampa.com/media/                        gs://media.gifttampa.com/
gsutil -m cp -a public-read -rn /var/storage/isladelsolfest.com/media/                   gs://media.isladelsolfest.com/
gsutil -m cp -a public-read -rn /var/storage/monsterblockparty.com/media/                gs://media.monsterblockparty.com/
gsutil -m cp -a public-read -rn /var/storage/smftampa.com/media/                         gs://media.smftampa.com/
gsutil -m cp -a public-read -rn /var/storage/somethingwicked.com/media/                  gs://media.somethingwicked.com/
gsutil -m cp -a public-read -rn /var/storage/suncitymusicfestival.com/media/             gs://media.suncitymusicfestival.com/
gsutil -m cp -a public-read -rn /var/storage/winterfantasyrgv.com/media/                 gs://media.winterfantasyrgv.com/
gsutil -m cp -a public-read -rn /var/storage/umesouthpadre.com/media/                    gs://media.umesouthpadre.com/
```

#### Archived Sites' Media
```
gsutil -m cp -a public-read -rn /var/storage/2014.dayafter.com/media/                    gs://media.dayafter.com/
gsutil -m cp -a public-read -rn /var/storage/2013.monsterblockparty.com/media/           gs://media.monsterblockparty.com/
gsutil -m cp -a public-read -rn /var/storage/2013.freaksbeatstreats.com/media/           gs://media.freaksbeatstreats.com/
```

#### Archived Sites' Media

```
gsutil -m cp -a public-read -rn /var/storage/hififest.com/media/                         gs://discodonniepresents.com/hififest.com
gsutil -m cp -a public-read -rn /var/storage/bassodyssey.com/media/                      gs://discodonniepresents.com/bassodyssey.com
gsutil -m cp -a public-read -rn /var/storage/wildwood.beachblanketfestival.com/media/    gs://discodonniepresents.com/wildwood.beachblanketfestival.com
gsutil -m cp -a public-read -rn /var/storage/galveston.beachblanketfestival.com/media/   gs://discodonniepresents.com/galveston.beachblanketfestival.com
gsutil -m cp -a public-read -rn /var/storage/mexico.lightsallnight.com/media/            gs://discodonniepresents.com/mexico.lightsallnight.com
gsutil -m cp -a public-read -rn /var/storage/sugarsociety.com/media/                     gs://discodonniepresents.com/sugarsociety.com
gsutil -m cp -a public-read -rn /var/storage/gxgmag.com/media/                           gs://discodonniepresents.com/gxgmag.com
```

### Technologies Used
There are several technologies that are used in our stack. Initially you won't have to work with them and we can explore each in more detail later.

* Varnish - All requests to production and develop branch use Varnish for caching.
* PageSpeed - This is a Google middleware that optimizes HTML output on the fly.
* ElasticSearch - We use this for searching and filtering. All Events are synchronized between WordPress in an ElasticSearch index.
* HHVM - Any PHP code has to be HHVM compatible since we'll be switching our production server to this shortly.
* Docker - The site's application code is packaged into a Docker image before being pushed to production.
* Gsutil - A CLI tool for accessing Google Cloud Storage files, which have all media uploads, MySQL dumps, etc.


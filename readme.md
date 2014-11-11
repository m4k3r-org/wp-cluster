### Setting up Local Environment

```
cd ~/Sites/discodonniepresents.com
git clone git@github.com:DiscoDonniePresents/www.discodonniepresents.com.git --depth 1 --branch develop .
export NODE_ENV=develop
export WP_ENV=develop
export DB_NAME=edm_develop
export DB_USER=root
export DB_PASSWORD=root
export DB_HOST=localhost
npm install
make snapshotImport
wp cloud sites
```

### Cache Purging
To purge Varnish cache, run the following commands. Be advised, Varnish will only accept purge notifications from accepted IP addresses.

```
curl -X PURGE discodonniepresents.com
curl -X PURGE dayafter.com
curl -X PURGE umesouthpadre.com
```

Otherwise you may simply `make varnishPurge`.

### Subtrees
Add "subtree helpers" to your bash profile. (https://gist.github.com/andypotanin/e54a7322da3fa33ada7e) to simplify subtree adding/pulling/pushing:

#### Pull Subtree Changes

```
pullSubtree UsabilityDynamics/wp-amd                      wp-content/plugins/wp-amd
pullSubtree UsabilityDynamics/wp-cluster                  wp-content/plugins/wp-cluster
pullSubtree UsabilityDynamics/wp-crm                      wp-content/plugins/wp-crm
pullSubtree UsabilityDynamics/wp-github-updater           wp-content/plugins/wp-github-updater
pullSubtree UsabilityDynamics/wp-network                  wp-content/plugins/wp-network
pullSubtree UsabilityDynamics/wp-pagespeed                wp-content/plugins/wp-pagespeed
pullSubtree UsabilityDynamics/wp-social-stream            wp-content/plugins/wp-social-stream
pullSubtree UsabilityDynamics/wp-splash                   wp-content/themes/wp-splash-v1.0      v1.0
pullSubtree UsabilityDynamics/wp-splash                   wp-content/themes/wp-splash-v2.0      v2.0
pullSubtree UsabilityDynamics/wp-veneer                   wp-content/plugins/wp-veneer
pullSubtree wpCloud/wp-vertical-edm                       wp-content/plugins/wp-vertical-edm
pullSubtree DiscoDonniePresents/wp-disco                  wp-content/themes/wp-disco-v1.0       v1.0
pullSubtree DiscoDonniePresents/wp-disco                  wp-content/themes/wp-disco-v2.0       v2.0
pullSubtree DiscoDonniePresents/wp-spectacle              wp-content/themes/wp-spectacle-v1.0   v1.0
pullSubtree DiscoDonniePresents/wp-spectacle              wp-content/themes/wp-spectacle-v2.0   v2.0
pullSubtree DiscoDonniePresents/wp-festival               wp-content/themes/wp-festival-v1.0    v1.0
pullSubtree DiscoDonniePresents/wp-festival               wp-content/themes/wp-festival-v2.0    v2.0
pullSubtree DiscoDonniePresents/wp-spectacle-chmf         wp-content/themes/wp-spectacle-chmf
pullSubtree DiscoDonniePresents/wp-spectacle-mbp          wp-content/themes/wp-spectacle-mbp
pullSubtree DiscoDonniePresents/wp-spectacle-fbt          wp-content/themes/wp-spectacle-fbt
pullSubtree DiscoDonniePresents/wp-spectacle-isladelsol   wp-content/themes/wp-spectacle-isladelsol
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

### Staging

* Now, when commiting to the 'develop' branch, your changes will be automatically deployed to the following domain name:
  {domain}.drop.ud-dev.com, i.e. dayafter.com becomes "dayafter-com.drop.ud-dev.com"
* In addition, we have a database backup done daily, that can be restored by including the following text in your commit message:
  [drop refreshdb]


### Media Sync

#### Standard Sites' Media
```
gsutil -m rsync -rd  ./wp-content/storage/beachblanketfestival.com/media/             gs://media.beachblanketfestival.com/
gsutil -m rsync -rd  ./wp-content/storage/cominghomemusicfestival.com/media/          gs://media.cominghomemusicfestival.com/
gsutil -m rsync -rd  ./wp-content/storage/dayafter.com/media/                         gs://media.dayafter.com/
gsutil -m rsync -rd  ./wp-content/storage/discodonniepresents.com/media/              gs://media.discodonniepresents.com/
gsutil -m rsync -rd  ./wp-content/storage/freaksbeatstreats.com/media/                gs://media.freaksbeatstreats.com/
gsutil -m rsync -rd  ./wp-content/storage/gifttampa.com/media/                        gs://media.gifttampa.com/
gsutil -m rsync -rd  ./wp-content/storage/isladelsolfest.com/media/                   gs://media.isladelsolfest.com/
gsutil -m rsync -rd  ./wp-content/storage/monsterblockparty.com/media/                gs://media.monsterblockparty.com/
gsutil -m rsync -rd  ./wp-content/storage/smftampa.com/media/                         gs://media.smftampa.com/
gsutil -m rsync -rd  ./wp-content/storage/somethingwicked.com/media/                  gs://media.somethingwicked.com/
gsutil -m rsync -rd  ./wp-content/storage/suncitymusicfestival.com/media/             gs://media.suncitymusicfestival.com/
gsutil -m rsync -rd  ./wp-content/storage/winterfantasyrgv.com/media/                 gs://media.winterfantasyrgv.com/
gsutil -m rsync -rd  ./wp-content/storage/umesouthpadre.com/media/                    gs://media.umesouthpadre.com/
```

#### Archived Sites' Media
```
gsutil -m rsync -rd  ./wp-content/storage/hififest.com/media/                         gs://ddpsdixyeejhwkgg.wpcloud.zone/media/hififest.com
gsutil -m rsync -rd  ./wp-content/storage/bassodyssey.com/media/                      gs://ddpsdixyeejhwkgg.wpcloud.zone/media/bassodyssey.com
gsutil -m rsync -rd  ./wp-content/storage/wildwood.beachblanketfestival.com/media/    gs://ddpsdixyeejhwkgg.wpcloud.zone/media/wildwood.beachblanketfestival.com
gsutil -m rsync -rd  ./wp-content/storage/galveston.beachblanketfestival.com/media/   gs://ddpsdixyeejhwkgg.wpcloud.zone/media/galveston.beachblanketfestival.com
gsutil -m rsync -rd  ./wp-content/storage/mexico.lightsallnight.com/media/            gs://ddpsdixyeejhwkgg.wpcloud.zone/media/mexico.lightsallnight.com
gsutil -m rsync -rd  ./wp-content/storage/2014.dayafter.com/media/                    gs://ddpsdixyeejhwkgg.wpcloud.zone/media/2014.dayafter.com
gsutil -m rsync -rd  ./wp-content/storage/2013.monsterblockparty.com/media/           gs://ddpsdixyeejhwkgg.wpcloud.zone/media/2013.monsterblockparty.com
gsutil -m rsync -rd  ./wp-content/storage/2013.freaksbeatstreats.com/media/           gs://ddpsdixyeejhwkgg.wpcloud.zone/media/2013.freaksbeatstreats.com
gsutil -m rsync -rd  ./wp-content/storage/sugarsociety.com/media/                     gs://ddpsdixyeejhwkgg.wpcloud.zone/media/sugarsociety.com
gsutil -m rsync -rd  ./wp-content/storage/gxgmag.com/media/                           gs://ddpsdixyeejhwkgg.wpcloud.zone/media/gxgmag.com
```

#### Archived Sites' Media (Broken)
```
gsutil -m rsync -rd  ./wp-content/storage/2015.umesouthpadre.com/media/               gs://ddpsdixyeejhwkgg.wpcloud.zone/media/2015.umesouthpadre.com
```

#### Media Permissions
```
gsutil -m setacl -R -a public-read gs://media.beachblanketfestival.com
gsutil -m setacl -R -a public-read gs://media.cominghomemusicfestival.com
gsutil -m setacl -R -a public-read gs://media.dayafter.com
gsutil -m setacl -R -a public-read gs://media.discodonniepresents.com
gsutil -m setacl -R -a public-read gs://media.freaksbeatstreats.com
gsutil -m setacl -R -a public-read gs://media.gifttampa.com
gsutil -m setacl -R -a public-read gs://media.isladelsolfest.com
gsutil -m setacl -R -a public-read gs://media.monsterblockparty.com
gsutil -m setacl -R -a public-read gs://media.smftampa.com
gsutil -m setacl -R -a public-read gs://media.somethingwicked.com
gsutil -m setacl -R -a public-read gs://media.suncitymusicfestival.com
gsutil -m setacl -R -a public-read gs://media.winterfantasyrgv.com
gsutil -m setacl -R -a public-read gs://media.umesouthpadre.com
gsutil -m setacl -R -a public-read gs://ddpsdixyeejhwkgg.wpcloud.zone/media
```

### Archive Sync

```
gsutil -m rsync -rd  ./wp-content/storage/2014.dayafter.com/media/                gs://2014.dayafter.com/
```
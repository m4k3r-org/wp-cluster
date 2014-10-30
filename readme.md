### Subtrees
Add "subtree helpers" to your bash profile. (https://gist.github.com/andypotanin/e54a7322da3fa33ada7e) to simplify subtree adding/pulling/pushing:

```
makeSubtree UsabilityDynamics/wp-veneer           vendor/plugins/wp-veneer
makeSubtree UsabilityDynamics/wp-cluster          vendor/plugins/wp-cluster
makeSubtree UsabilityDynamics/wp-elastic          vendor/plugins/wp-elastic
makeSubtree UsabilityDynamics/wp-network          vendor/plugins/wp-network
makeSubtree UsabilityDynamics/wp-github-updater   vendor/plugins/wp-network
makeSubtree UsabilityDynamics/wp-splash           vendor/themes/wp-splash
makeSubtree wpCloud/wp-vertical-edm               vendor/plugins/wp-vertical-edm
makeSubtree wpCloud/wp-event-post-type            vendor/plugins/wp-event-post-type
```

```
makeSubtree DiscoDonniePresents/wp-disco          vendor/themes/wp-disco-v1.0       v1.0
makeSubtree DiscoDonniePresents/wp-disco          vendor/themes/wp-disco-v2.0       v2.0
makeSubtree DiscoDonniePresents/wp-festival       vendor/themes/wp-festival-v1.0    v1.0
makeSubtree DiscoDonniePresents/wp-festival       vendor/themes/wp-festival-v2.0    v2.0
makeSubtree DiscoDonniePresents/wp-spectacle      vendor/themes/wp-spectacle-v1.0   v1.0
makeSubtree DiscoDonniePresents/wp-spectacle      vendor/themes/wp-spectacle-v2.0   v2.0
makeSubtree DiscoDonniePresents/wp-spectacle-chmf  vendor/themes/wp-spectacle-chmf
makeSubtree DiscoDonniePresents/wp-spectacle-mbp  vendor/themes/wp-spectacle-mbp
makeSubtree DiscoDonniePresents/wp-spectacle-fbt  vendor/themes/wp-spectacle-fbt
makeSubtree DiscoDonniePresents/wp-spectacle-isladelsol  vendor/themes/wp-spectacle-isladelsol
```

```
pushSubtree UsabilityDynamics/wp-cluster            vendor/plugins/wp-cluster
pushSubtree DiscoDonniePresents/wp-spectacle-mbp    vendor/themes/wp-spectacle-mbp
pushSubtree DiscoDonniePresents/wp-disco            vendor/themes/wp-disco-v2.0 v2.0
pushSubtree DiscoDonniePresents/wp-spectacle      vendor/themes/wp-spectacle-v2.0   v2.0
pushSubtree DiscoDonniePresents/wp-festival       vendor/themes/wp-festival-v2.0    v2.0
```
```
pullSubtree DiscoDonniePresents/wp-spectacle        vendor/themes/wp-spectacle-v1.0   v1.0
pullSubtree DiscoDonniePresents/wp-spectacle        vendor/themes/wp-spectacle-v2.0   v2.0
pullSubtree DiscoDonniePresents/wp-festival         vendor/themes/wp-festival-v2.0    v2.0
pullSubtree DiscoDonniePresents/wp-spectacle-chmf   vendor/themes/wp-spectacle-chmf
pullSubtree DiscoDonniePresents/wp-spectacle-mbp    vendor/themes/wp-spectacle-mbp
pullSubtree DiscoDonniePresents/wp-spectacle-fbt    vendor/themes/wp-spectacle-fbt
pullSubtree DiscoDonniePresents/wp-spectacle-isladelsol  vendor/themes/wp-spectacle-isladelsol
```

Show installed libs:
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

### MySQL Backup and Restore
Create Backup, either run "make snapshot" to create an automatic snapshot that uses branch name, or create a manually DB backup:
```
wp transient delete-all && wp cache flush
wp db export edm_production.sql
tar cvzf edm_production.sql.tgz edm_production.sql
s3cmd put --no-check-md5 --reduced-redundancy edm_production.sql.tgz s3://rds.uds.io/DiscoDonniePresents/www.discodonniepresents.com/edm_production.sql.tgz
mv  edm_production.sql**
```

To fetch backup locally and import it:
```
s3cmd get s3://rds.uds.io/DiscoDonniePresents/www.discodonniepresents.com/edm_production.sql.tgz
tar xvf edm_production.sql.tgz
wp db import edm_production.sql
```


### Media Sync

```
gsutil -m rsync -rd  /var/www/storage/public/dayafter.com/media/                gs://media.dayafter.com/
gsutil -m rsync -rd  /var/www/storage/public/somethingwicked.com/media/         gs://media.somethingwicked.com/
gsutil -m rsync -rd  /var/www/storage/public/freaksbeatstreats.com/media/       gs://media.freaksbeatstreats.com/
gsutil -m rsync -rd  /var/www/storage/public/monsterblockparty.com/media/       gs://media.monsterblockparty.com/
gsutil -m rsync -rd  /var/www/storage/public/cominghomemusicfestival.com/media/ gs://media.cominghomemusicfestival.com/
gsutil -m rsync -rd  /var/www/storage/public/discodonniepresents.com/media/     gs://media.discodonniepresents.com/
gsutil -m rsync -rd  /var/www/storage/public/smftampa.com/media/                gs://media.smftampa.com/
gsutil -m rsync -rd  /var/www/storage/public/suncitymusicfestival.com/media/    gs://media.suncitymusicfestival.com/
gsutil -m rsync -rd  /var/www/storage/public/isladelsolfest.com/media/          gs://media.isladelsolfest.com/
```

```
gsutil -m setacl -R -a public-read gs://media.dayafter.com
gsutil -m setacl -R -a public-read gs://media.somethingwicked.com
gsutil -m setacl -R -a public-read gs://media.freaksbeatstreats.com
gsutil -m setacl -R -a public-read gs://media.monsterblockparty.com
gsutil -m setacl -R -a public-read gs://media.cominghomemusicfestival.com
gsutil -m setacl -R -a public-read gs://media.discodonniepresents.com
gsutil -m setacl -R -a public-read gs://media.smftampa.com
gsutil -m setacl -R -a public-read gs://media.suncitymusicfestival.com
```

### Archive Sync

```
gsutil -m rsync -rd  /var/www/storage/public/2014.dayafter.com/media/                gs://2014.dayafter.com/
```
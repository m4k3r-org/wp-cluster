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
makeSubtree DiscoDonniePresents/wp-disco          vendor/themes/wp-disco-v1.0 v1.0
makeSubtree DiscoDonniePresents/wp-disco          vendor/themes/wp-disco-v2.0 v2.0
makeSubtree DiscoDonniePresents/wp-festival       vendor/themes/wp-festival v1.0
makeSubtree DiscoDonniePresents/wp-festival       vendor/themes/wp-festival-2 v2.0
makeSubtree DiscoDonniePresents/wp-spectacle      vendor/themes/wp-spectacle v1.0
makeSubtree DiscoDonniePresents/wp-spectacle      vendor/themes/wp-spectacle-2 v2.0
makeSubtree DiscoDonniePresents/wp-spectacle-chmf  vendor/themes/wp-spectacle-chmf
makeSubtree DiscoDonniePresents/wp-spectacle-mbp  vendor/themes/wp-spectacle-mbp
makeSubtree DiscoDonniePresents/wp-spectacle-fbt  vendor/themes/wp-spectacle-fbt
makeSubtree DiscoDonniePresents/wp-spectacle-isladelsol  vendor/themes/wp-spectacle-isladelsol
```

```
pushSubtree DiscoDonniePresents/wp-disco          vendor/themes/wp-disco-v2.0 v2.0
```

There seem to be issues with pushing changes back to a lib-settings, probably because it's actually a tag.
```
makeSubtree UsabilityDynamics/lib-settings        vendor/libraries/usabilitydynamics/lib-settings 0.2.2
makeSubtree UsabilityDynamics/lib-utility         vendor/libraries/usabilitydynamics/lib-utility
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
rm -rf edm_production.sql**
```

To fetch backup locally and import it:
```
s3cmd get s3://rds.uds.io/DiscoDonniePresents/www.discodonniepresents.com/edm_production.sql.tgz
tar xvf edm_production.sql.tgz
wp db import edm_production.sql
```

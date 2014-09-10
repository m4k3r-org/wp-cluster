To create MySQL snapshots.

### OSX
```
brew install s3cmd
brew install gpg
s3cmd --configure
```

You should see the cdn.uds.io bucket.
```
s3cmd ls s3://cdn.uds.io
```

Push MySQL dump to bucket.
```
tar -cvzf edm_cluster_new.sql.tar.gz edm_cluster_new.sql
s3cmd -P put ./edm_cluster_new.sql.tar.gz s3://cdn.uds.io
```

Fetch MySQL dump from bucket.
```
s3cmd get s3://cdn.uds.io/edm_cluster_new.sql.tar.gz
tar -xvf edm_cluster_new.sql.tar.gz
```

```
tar -cvzf edm_cluster_new.sql.$(date '+%Y-%m-%d-%H-%m').tar.gz edm_cluster_new.sql
```
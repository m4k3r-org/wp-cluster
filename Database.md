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
s3cmd -P put ./edm_cluster_new.sql s3://cdn.uds.io
```

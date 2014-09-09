On production, to start a daemonized container, run the following command.


### Create New Blackbox
```
docker pull andypotanin/blackbox:0.7.0
docker tag andypotanin/blackbox:0.7.0 blackbox/ddp
```

### Use Existing Blackbox

```
docker pull blackbox/ddp
```

### Start Blackbox
```
docker run -itd \
  --name=ddp-cluster \
  --net=host \
  --sig-proxy=false \
  --privileged=true \
  --env=BLACKBOX_ORGANIZATION=ddps-dixy-eejh-wkgg \
  --env=BLACKBOX_GLUSTER_HOST=208.52.164.203:/ddps-dixy-eejh-wkgg \
  --env=BLACKBOX_GLUSTER_HOST=208.52.164.203:/ddps-dixy-eejh-wkgg \
  --env=DOCKER_PORT=2375 \
  blackbox/ddp /bin/bash
```

Attach to Blackbox container:
```
docker attach ddp-cluster
```

Pull images we will need:
```
docker pull andypotanin/sshd:0.2.0
docker pull andypotanin/hhvm:0.1.0
docker pull hipstack/hipstack:0.1.2
docker pull hipstack/wordpress:0.1.0
```

Clone repository for DDP. We assume that we're working with "production" branch.
```
mkdir -p /var/run/apps/www.discodonniepresents.com:production
git clone git@github.com:DiscoDonniePresents/www.discodonniepresents.com.git -b production /var/run/apps/www.discodonniepresents.com:production
```

```
docker run -dit \
  --privileged \
  --name=www.discodonniepresents.com \
  --hostname=www.discodonniepresents.com \
  --volume=/storage/storage.discodonniepresents.com:/var/storage \
  --volume=/root/.ssh:/root/.ssh \
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
  --entrypoint=/var/www/application/bin/bash/docker.entrypoint.sh \
  discodonniepresents/www.discodonniepresents.com /bin/bash
```

This command assumes that "storage" must reside on the host machine in /media/storage.discodonniepresents.com.

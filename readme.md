### Container Setup
`docker pull discodonniepresents/www.discodonniepresents.com`
`docker import http://images.wpcloud.io/www.discodonniepresents.com.tgz`


### Modular Development
To start container and expose themes volume for development:

```sh
docker run -td -P \
  -v /var/www/vendor/themes \
  -v /var/www/vendor/plugins \
  discodonniepresents/www.discodonniepresents.com:0.0.1 \
  /usr/bin/supervisord -n
```

### Container Development

Clone GitHub Project.
`composer create-project discodonniepresents/www.discodonniepresents.com`

Create Distribution
`docker save discodonniepresents/www.discodonniepresents.com > discodonniepresents/www.discodonniepresents.com.tgz`

Bash-in
`docker run -ti -P --rm discodonniepresents/www.discodonniepresents.com:0.0.1 /bin/bash`


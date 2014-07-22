### Setup Environment
`docker pull discodonniepresents/www.discodonniepresents.com`
`docker import http://images.wpcloud.io/www.discodonniepresents.com.tgz`
`docker run -d -v /var/www/vendor/themes discodonniepresents/www.discodonniepresents.com`

### System Development

Clone GitHub Project.
`composer create-project discodonniepresents/www.discodonniepresents.com`

Create Distribution
`docker save discodonniepresents/www.discodonniepresents.com > discodonniepresents/www.discodonniepresents.com.tgz`
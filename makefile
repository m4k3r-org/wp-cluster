## Build DDP Site/Network
##
## docker build -t discodonniepresents/www.discodonniepresents.com:0.1.0 --rm .
##

NAME 			= discodonniepresents/www.discodonniepresents.com
VERSION 	= 0.1.0

default:
	make install

# Build Docker Image for deployment
docker:
	cp application/static/etc/Dockerfile ./Dockerfile
	docker build -t $(NAME):$(VERSION) --rm .
	unlink ./Dockerfile

# Build Docker Image for deployment
release:
	docker tag discodonniepresents/www.discodonniepresents.com discodonniepresents/www.discodonniepresents.com:0.1.0
	docker push discodonniepresents/www.discodonniepresents.com:0.1.0

# Build for Distribution
build:
	npm install --silent --production
	application/bin/composer install --prefer-dist
	grunt install --environment=production --system=linux --type=cluster

# Install for Development
install:
	npm install --silent --development
	application/bin/composer install --prefer-source
	grunt install --environment=development --type=cluster

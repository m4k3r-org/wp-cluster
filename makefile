################################################################################################
## Build DDP Site/Network
##
## ### Build and Push
## docker build -t  andypotanin/www.discodonniepresents.com:latest --rm .
## docker push      andypotanin/www.discodonniepresents.com:latest
##
## ### Pull and Run
## docker pull      andypotanin/www.discodonniepresents.com:latest
## docker run       --name edm -d -v /var/www/ andypotanin/www.discodonniepresents.com:latest
##
################################################################################################

ORGANIATION  = andypotanin
NAME 			   = www.discodonniepresents.com
VERSION 	   = latest

default:
	make install

# Build Docker Image for deployment
docker:
	docker build -t $(ORGANIATION)/$(NAME):$(VERSION) --rm .

# Build Docker Image for deployment
release:
	make docker
	docker push $(ORGANIATION)/$(NAME):$(VERSION)

# Build for Distribution
build:
	npm install --production
	application/bin/composer install --prefer-dist
	grunt install --environment=production --system=linux --type=cluster

# Install for Staging/Development
install:
	npm install --development
	application/bin/composer install --prefer-source
	grunt install --environment=development --type=cluster
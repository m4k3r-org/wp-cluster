################################################################################################
## Build DDP Site/Network
##
## This can be used to a build a "baseline" image.
##
## ### Build and Push
## docker build -t  discodonniepresents/www.discodonniepresents.com --rm .
## docker push      discodonniepresents/www.discodonniepresents.com
##
## ### Pull and Run
## docker pull      discodonniepresents/www.discodonniepresents.com
## docker run       --name edm -d -v /var/www/ discodonniepresents/www.discodonniepresents.com
##
## ### Commit and Push a Change
## docker commit -m="Setup www.discodonniepresents.com, tagged." furious_sammet
## docker tag 612a966410e5 discodonniepresents/www.discodonniepresents.com:0.0.1
## docker push discodonniepresents/www.discodonniepresents.com:0.0.1
##
################################################################################################

ORGANIATION   = discodonniepresents
NAME 			    = www.discodonniepresents.com
DOMAIN 	      = www.discodonniepresents.com.internal
VERSION 	    = baseline-2.0.1

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
	composer install --prefer-dist --no-dev --no-interaction
	grunt install --environment=production --system=linux --type=cluster

# Install for Staging/Development
install:
	npm install --production
	npm install --development
	composer install --prefer-source --dev --no-interaction
	grunt install --environment=development --type=cluster

# Install for Staging/Development
server:
	docker run -itd --name=$(DOMAIN) --hostname=$(DOMAIN).internal $(ORGANIATION)/$(NAME):$(VERSION)

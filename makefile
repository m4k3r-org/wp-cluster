## Build DDP Site/Network
##
## docker build -t  andypotanin/www.discodonniepresents.com:0.1.3 --rm .
## docker push      andypotanin/www.discodonniepresents.com:0.1.3
##
## docker pull      andypotanin/www.discodonniepresents.com:0.1.3
## docker run --name edm -d -v /home/edm/www:/var/www:rw  andypotanin/www.discodonniepresents.com:0.1.3
##

ORGANIATION  = andypotanin
NAME 			   = www.discodonniepresents.com
VERSION 	   = 0.1.3

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
	git clone -b v2 git@github.com:DiscoDonniePresents/wp-festival.git      vendor/themes/wp-festival-2.0.0
	git clone -b legacy git@github.com:DiscoDonniePresents/wp-festival.git  vendor/themes/wp-festival

# Install for Staging/Development
install:
	npm install --development
	application/bin/composer install --prefer-source
	grunt install --environment=development --type=cluster

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

ORGANIATION   = andypotanin
NAME 			    = www.discodonniepresents.com
VERSION 	    = latest
CWD           = $(shell pwd)

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
	application/bin/composer install --prefer-dist --no-dev --no-interaction
	grunt install --environment=production --system=linux --type=cluster

# Install for Staging/Development
install:
	npm install --production
	npm install --development
	application/bin/composer install --prefer-source --dev --no-interaction
	grunt install --environment=development --type=cluster

# Fetch and Build Themes
installThemes:
	git clone git@github.com:DiscoDonniePresents/wp-festival.git    -b legacy   vendor/themes/wp-festival
	git clone git@github.com:DiscoDonniePresents/wp-festival.git    -b v2       vendor/themes/wp-festival-2
	git clone git@github.com:DiscoDonniePresents/wp-disco.git       -b legacy   vendor/themes/wp-disco
	git clone git@github.com:DiscoDonniePresents/wp-dayafter.git    -b master   vendor/themes/wp-dayafter
	git clone git@github.com:usabilitydynamics/wp-splash.git        -b master   vendor/themes/wp-splash

	# cd ./vendor/themes/wp-festival && make
	# cd ./vendor/themes/wp-festival-2 && make
	# cd ./vendor/themes/wp-disco && make

# Fetch amd Build Plugins
installPlugins:
	git clone git@github.com:usabilitydynamics/wp-simplify.git      -b master   vendor/plugins/wp-simplify
	cd vendor/plugins/wp-simplify && composer install
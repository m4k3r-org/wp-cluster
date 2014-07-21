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
	application/bin/composer install --prefer-dist --no-dev --no-interaction
	grunt install --environment=production --system=linux --type=cluster

# Install for Staging/Development
install:
	npm install
	application/bin/composer install --prefer-source --no-dev --no-interaction
	grunt install --environment=development --type=cluster

# Fetch and Build Themes
installThemes:
	git clone git@github.com:DiscoDonniePresents/wp-festival.git    -b legacy   vendor/themes/wp-festival   && application/bin/composer -d vendor/themes/wp-festival install
	git clone git@github.com:DiscoDonniePresents/wp-festival.git    -b v2       vendor/themes/wp-festival-2 && application/bin/composer -d vendor/themes/wp-festival-2 install
	git clone git@github.com:DiscoDonniePresents/wp-disco.git       -b legacy   vendor/themes/wp-disco      && application/bin/composer -d vendor/themes/wp-disco install
	git clone git@github.com:DiscoDonniePresents/wp-dayafter.git    -b master   vendor/themes/wp-dayafter   && application/bin/composer -d vendor/themes/wp-dayafter install
	git clone git@github.com:usabilitydynamics/wp-splash.git        -b master   vendor/themes/wp-splash     && application/bin/composer -d vendor/themes/wp-splash install

# Fetch amd Build Plugins
installPlugins:
	git clone git@github.com:usabilitydynamics/wp-simplify.git      -b master   vendor/plugins/wp-simplify  && application/bin/composer -d vendor/plugins/wp-simplify install
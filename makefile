################################################################################################
## Build DDP Site/Network
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
##
################################################################################################

ORGANIATION   = discodonniepresents
NAME 			    = www.discodonniepresents.com
DOMAIN 	      = www.discodonniepresents.com.internal
VERSION 	    = 0.0.1

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

# Fetch amd Build Plugins
installPluggable:
	git clone git@github.com:DiscoDonniePresents/wp-festival.git -b legacy vendor/themes/wp-festival 2>/dev/null
	git clone git@github.com:DiscoDonniePresents/wp-festival.git -b v2 vendor/themes/wp-festival-2 2>/dev/null
	git clone git@github.com:DiscoDonniePresents/wp-dayafter.git -b master vendor/themes/wp-dayafter 2>/dev/null
	git clone git@github.com:DiscoDonniePresents/wp-disco.git -b legacy vendor/themes/wp-disco 2>/dev/null
	git clone git@github.com:usabilitydynamics/wp-splash.git -b legacy vendor/themes/wp-splash 2>/dev/null
	git clone git@github.com:usabilitydynamics/wp-simplify.git -b master vendor/plugins/wp-simplify 2>/dev/null
	git clone git@github.com:DiscoDonniePresents/wp-spectacle.git -b master vendor/themes/wp-spectacle 2>/dev/null
	git clone git@github.com:DiscoDonniePresents/wp-winterfantasy.git -b master vendor/themes/wp-winterfantasy 2>/dev/null
	git clone git@github.com:DiscoDonniePresents/wp-monsterblockparty.git -b master vendor/themes/wp-monsterblockparty 2>/dev/null

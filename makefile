################################################################################################
## Build DDP Site/Network
##
## ### Build and Push
## docker build -t  usabilitydynamics/www.discodonniepresents.com:latest --rm .
## docker push      usabilitydynamics/www.discodonniepresents.com:latest
##
## ### Pull and Run
## docker pull      usabilitydynamics/www.discodonniepresents.com:latest
## docker run       --name edm -d -v /var/www/ usabilitydynamics/www.discodonniepresents.com:latest
## docker run -itd --name=udx.io -p 8010 usabilitydynamics/udx.io:0.3.2 npm start
##
## ### Commit and Push a Change
## docker commit -m="Setup udx.io, tagged." furious_sammet
## docker tag 612a966410e5 usabilitydynamics/udx.io:0.3.2
## docker push usabilitydynamics/udx.io:0.3.2
##
##
################################################################################################

ORGANIATION   = usabilitydynamics
NAME 			    = www.discodonniepresents.com
DOMAIN 	      = edm.server
VERSION 	    = latest

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

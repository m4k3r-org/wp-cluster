################################################################################################
## Build DDP Site/Network
##
## ### Build and Push
## docker build -t  andypotanin/www.discodonniepresents.com:0.1.6 --rm .
## docker push      andypotanin/www.discodonniepresents.com:0.1.6
##
## ### Pull and Run
## docker pull      andypotanin/www.discodonniepresents.com:0.1.6
## docker run --name edm -d -v /var/www/ andypotanin/www.discodonniepresents.com:0.1.6
##
################################################################################################

ORGANIATION  = andypotanin
NAME 			   = www.discodonniepresents.com
VERSION 	   = 0.1.6

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
	git clone git@github.com:DiscoDonniePresents/wp-festival.git            -b legacy   vendor/themes/wp-festival | \
	git clone git@github.com:DiscoDonniePresents/wp-festival.git            -b v2       vendor/themes/wp-festival-2 | \
	git clone git@github.com:DiscoDonniePresents/wp-disco.git               -b legacy   vendor/themes/wp-disco | \
	git clone git@github.com:DiscoDonniePresents/wp-bassoddysey.git         -b master   vendor/themes/wp-bassoddysey | \
	git clone git@github.com:DiscoDonniePresents/wp-dayafter.git            -b master   vendor/themes/wp-dayafter | \
	git clone git@github.com:DiscoDonniePresents/wp-freaksbeatstreats.git   -b master   vendor/themes/wp-freaksbeatstreats | \
	git clone git@github.com:DiscoDonniePresents/wp-hififest.git            -b master   vendor/themes/wp-hififest | \
	git clone git@github.com:DiscoDonniePresents/wp-lanmexico.git           -b master   vendor/themes/wp-lanmexico | \
	git clone git@github.com:DiscoDonniePresents/wp-monsterblockparty.git   -b master   vendor/themes/wp-monsterblockparty | \
	git clone git@github.com:DiscoDonniePresents/wp-thegift.git             -b master   vendor/themes/wp-thegift | \
	git clone git@github.com:DiscoDonniePresents/wp-winterfantasy.git       -b master   vendor/themes/wp-winterfantasy | \
	git clone git@github.com:usabilitydynamics/wp-splash.git                -b master   vendor/themes/wp-splash | \
	git clone git@github.com:usabilitydynamics/wp-simplify.git              -b master   vendor/plugins/wp-simplify

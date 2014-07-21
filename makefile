## Build DDP Site/Network
##
## docker build -t discodonniepresents/www.discodonniepresents.com:0.1.0 --rm .
## docker tag discodonniepresents/www.discodonniepresents.com andypotanin/www.discodonniepresents.com:0.1.1
## docker push andypotanin/www.discodonniepresents.com:0.1.1
##
## docker pull andypotanin/www.discodonniepresents.com:0.1.1
## docker run --name www.discodonniepresents.com -d -v /home/edm/_www:/var/www:rw  andypotanin/www.discodonniepresents.com:0.1.1
##

ORGANIATION  = discodonniepresents
NAME 			   = www.discodonniepresents.com
VERSION 	   = latest

default:
	make install

# Build Docker Image for deployment
docker:
	docker build -t $(NAME):$(VERSION) --rm .

# Build Docker Image for deployment
release:
	docker tag $(ORGANIATION)/$(NAME) $(ORGANIATION)/$(NAME):$(VERSION)
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

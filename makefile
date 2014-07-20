## Build DDP Site/Network
##
## docker build -t discodonniepresents/www.discodonniepresents.com:0.1.0 --rm .
##

ORGANIATION  = discodonniepresents
NAME 			   = discodonniepresents/www.discodonniepresents.com
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

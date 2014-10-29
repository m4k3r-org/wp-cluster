## Build Plugin
##
##

NAME = wp-elastic

default:
	make install

# Build for Distribution
build:
	echo Building $(NAME).
	npm install --production
	composer install --prefer-dist --no-dev --no-interaction
	grunt build

# Build for repository commit
push:
	echo Pushing $(NAME).
	rm -rf composer.lock
	composer update --prefer-dist --no-dev --no-interaction
	git add . --all
	git commit -m '[ci skip]'
	git push

# Install for Staging/Development
install:
	echo Installing $(NAME).
	npm install --production
	npm install --development
	composer install --prefer-source --dev  --no-interaction
	grunt install

## Build WP-Disco
##
##

NAME 			   = wp-disco
VERSION 	   = v1

default:
	make install

# Build for Distribution
build:
	echo Building $(NAME):$(VERSION).
	npm install --production
	php /usr/bin/composer.phar install --prefer-dist
	grunt build

# Install for Staging/Development
install:
	echo Installing $(NAME):$(VERSION).
	npm install --development
	php /usr/bin/composer.phar install --prefer-source
	grunt install

# Build for repository commit
release:
	@echo Releasing $(NAME).
	@rm -rf vendor/composer/installers
	@rm -rf vendor/composer/installed.json
	@composer update --prefer-dist --no-dev --no-interaction
	@git add . --all && git commit -m '[ci skip]' && git push

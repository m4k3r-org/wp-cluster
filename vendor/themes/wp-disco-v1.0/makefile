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

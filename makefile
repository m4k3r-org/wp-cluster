## Build Plugin
##
##

NAME 			   = wp-amd
VERSION 	   = v2

default:
	make install

# Build for Distribution
build:
	echo Building $(NAME).
	npm install --production
	php vendor/bin/composer install --prefer-dist
	grunt build

# Install for Staging/Development
install:
	echo Installing $(NAME).
	npm install --development
	php vendor/bin/composer install --prefer-source
	grunt install

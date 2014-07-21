## Build WP-Festival
##
##
##

NAME 			  = wp-festival

default:
	make install

# Build for Distribution
build:
	@echo Building $(NAME).
	npm install --production
	composer install --prefer-dist --no-dev --no-interaction
	grunt build

# Install for Staging/Development
install:
	@echo Installing $(NAME).
	npm install --development
	npm install --production
	composer install --prefer-source --dev --no-interaction
	grunt install

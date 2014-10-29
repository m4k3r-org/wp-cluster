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

# Build for repository commit
#
# @git rm --cached -r --ignore-unmatch vendor/libraries/usabilitydynamics/lib-utility
#
release:
	@echo Releasing $(NAME).
	@rm -rf vendor/libraries/composer/installers
	@rm -rf vendor/libraries/composer/installed.json
	@composer update --prefer-dist --no-dev --no-interaction
	@git add . --all && git commit -m '[ci skip]' && git push

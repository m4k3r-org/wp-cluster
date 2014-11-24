## Build Plugin
##
##

NAME = wp-veneer

# Default Install Action
default:
	make install

# Build for Distribution
build:
	echo Building $(NAME).
	npm install --production
	composer install --prefer-dist --no-dev --no-interaction
	grunt build

# Build for Distribution
install:
	@echo Installing $(NAME).
	rm -rf composer.lock
	rm -rf vendor
	composer update --prefer-dist --no-dev --no-interaction

# Build for repository commit
release:
	make install
	rm -rf vendor/composer/installers
	git rm --cached -r --ignore-unmatch vendor/usabilitydynamics/lib-api
	git rm --cached -r --ignore-unmatch vendor/usabilitydynamics/lib-ui
	git rm --cached -r --ignore-unmatch vendor/usabilitydynamics/lib-utility
	git rm --cached -r --ignore-unmatch vendor/usabilitydynamics/lib-settings
	git add . --all && git commit -m '[ci skip]' && git push

## Build Plugin
##
##

NAME = wp-cluster

# Default Install Action
default:
	make install

# Build for Distribution
build:
	echo Building $(NAME).
	npm install
	composer install --prefer-dist --no-dev --no-interaction
	grunt build

# Build for repository commit
push:
	rm -rf composer.lock
	echo Pushing $(NAME).
	composer update --prefer-dist --no-dev --no-interaction
	git add . --all
	git commit -m '[ci skip]'
	git push

# Install for Staging/Development
install:
	echo Installing $(NAME).
	npm install
	npm install --dev
	composer install --prefer-source --dev --no-interaction
	grunt install

# Build for repository commit
#
# Should be added for any dependencies that don't have a distribution:
# @git rm --cached -r --ignore-unmatch vendor/libraries/usabilitydynamics/lib-utility
#
release:
	@echo Releasing $(NAME).
	@rm -rf vendor/libraries/composer/installers
	@rm -rf vendor/libraries/composer/installed.json
	@composer update --prefer-dist --no-dev --no-interaction
	@grunt install
	@git add . --all && git commit -m 'Built release. [ci skip]' && git push

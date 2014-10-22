## Build Theme
##
##

NAME = wp-spectacle

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
	@echo Releasing $(NAME).
	make install
	rm -rf vendor/composer/installed.json
	rm -rf vendor/composer/installers
	git rm --cached -r --ignore-unmatch vendor/plugins/siteorigin-panels
	git rm --cached -r --ignore-unmatch vendor/usabilitydynamics/lib-api
	git rm --cached -r --ignore-unmatch vendor/usabilitydynamics/lib-meta-box
	git rm --cached -r --ignore-unmatch vendor/usabilitydynamics/lib-model
	git rm --cached -r --ignore-unmatch vendor/usabilitydynamics/lib-requires
	git rm --cached -r --ignore-unmatch vendor/usabilitydynamics/lib-rpc
	git rm --cached -r --ignore-unmatch vendor/usabilitydynamics/lib-settings
	git rm --cached -r --ignore-unmatch vendor/usabilitydynamics/lib-ui
	git rm --cached -r --ignore-unmatch vendor/usabilitydynamics/lib-utility
	git rm --cached -r --ignore-unmatch vendor/usabilitydynamics/lib-wp-theme
	lessc -x ./static/styles/src/app.less > ./static/styles/app.css
	git add . --all && git commit -m 'Release build. [ci skip]' && git push

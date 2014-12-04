################################################################################################
## Mobile Theme
##
################################################################################################

CURRENT_BRANCH                ?=$(shell git describe --contains --all HEAD)
CURRENT_COMMIT                ?=$(shell git rev-list -1 HEAD)
CURRENT_TAG                   ?=$(shell git describe --always --tag)

##
##
##
default:
	@make install

## Install
##
##
install:
	@npm install
	@bower install
	@grunt install

## Build all
##
##
build:
	@echo "Building all assets."
	@grunt build

##
##
##
release:
	@echo "Release build..."
	@@grunt release

## pass commands to Grunt
##
##
%:
	@npm install
	@grunt $@



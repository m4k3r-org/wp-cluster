################################################################################################
## Build DDP Network
##
##
## Manual MySQL Dump:
##    wp db export
##    gzip edm_production.sql
##    gsutil -m mv edm_production.sql.gz gs://discodonniepresents.com/
##
## export ACCOUNT_NAME=edm
## export CURRENT_BRANCH=$(git describe --contains --all HEAD)
##
################################################################################################

CIRCLE_PROJECT_USERNAME	      ?=DiscoDonniePresents
CIRCLE_PROJECT_REPONAME	      ?=www.discodonniepresents.com
CURRENT_BRANCH                ?=$(shell git describe --contains --all HEAD)
CURRENT_COMMIT                ?=$(shell git rev-list -1 HEAD)
CURRENT_TAG                   ?=$(shell git describe --always --tag)
ACCOUNT_NAME		              ?=edm
STORAGE_DIR		                ?=/var/storage/
STORAGE_BUCKET		            ?=gs://discodonniepresents.com
RDS_BUCKET		                ?=s3://rds.uds.io/DiscoDonniePresents/www.discodonniepresents.com
SITE_LIST		                  ?=$(shell wp --allow-root site list --field=url --format=csv)
PWD                           := $(shell pwd)
_GET_SITES                    =$(call wp,sites,list)

#
#
default: install

# Install direnv (https://github.com/zimbatm/direnv) to use this file
# - Run "direnv allow" to enable.
#
setEnvironment:
	@touch ./.envrc
	@echo "Enabled environment variables in .envrc."

# Pull all Subtrees
#
freshTrees:
	@git subtree pull --prefix=wp-content/static/wiki git@github.com:DiscoDonniePresents/www.discodonniepresents.com.wiki master --squash
	@git subtree pull --prefix=wp-content/plugins/wp-amd git@github.com:UsabilityDynamics/wp-amd master --squash
	@git subtree pull --prefix=wp-content/plugins/wp-cluster git@github.com:UsabilityDynamics/wp-cluster master --squash
	@git subtree pull --prefix=wp-content/plugins/wp-crm git@github.com:UsabilityDynamics/wp-crm master --squash
	@git subtree pull --prefix=wp-content/plugins/wp-amd git@github.com:UsabilityDynamics/wp-amd master --squash
	@git subtree pull --prefix=wp-content/plugins/wp-cluster git@github.com:UsabilityDynamics/wp-cluster master --squash
	@git subtree pull --prefix=wp-content/plugins/wp-crm git@github.com:UsabilityDynamics/wp-crm master --squash
	@git subtree pull --prefix=wp-content/plugins/wp-github-updater git@github.com:UsabilityDynamics/wp-github-updater master --squash
	@git subtree pull --prefix=wp-content/plugins/wp-network git@github.com:UsabilityDynamics/wp-network master --squash
	@git subtree pull --prefix=wp-content/plugins/wp-pagespeed git@github.com:UsabilityDynamics/wp-pagespeed master --squash
	@git subtree pull --prefix=wp-content/plugins/wp-social-stream git@github.com:UsabilityDynamics/wp-social-stream master --squash
	@git subtree pull --prefix=wp-content/themes/wp-splash-v1.0 git@github.com:UsabilityDynamics/wp-splash v1.0 --squash
	@git subtree pull --prefix=wp-content/themes/wp-splash-v2.0 git@github.com:UsabilityDynamics/wp-splash v2.0 --squash
	@git subtree pull --prefix=wp-content/plugins/wp-veneer git@github.com:UsabilityDynamics/wp-veneer master --squash
	@git subtree pull --prefix=wp-content/plugins/wp-vertical-edm git@github.com:wpCloud/wp-vertical-edm master --squash
	@git subtree pull --prefix=wp-content/themes/wp-disco-v1.0 git@github.com:DiscoDonniePresents/wp-disco v1.0 --squash
	@git subtree pull --prefix=wp-content/themes/wp-disco-v2.0 git@github.com:DiscoDonniePresents/wp-disco v2.0 --squash
	@git subtree pull --prefix=wp-content/themes/wp-spectacle-v1.0 git@github.com:DiscoDonniePresents/wp-spectacle v1.0 --squash
	@git subtree pull --prefix=wp-content/themes/wp-spectacle-v2.0 git@github.com:DiscoDonniePresents/wp-spectacle v2.0 --squash
	@git subtree pull --prefix=wp-content/themes/wp-festival-v1.0 git@github.com:DiscoDonniePresents/wp-festival v1.0 --squash
	@git subtree pull --prefix=wp-content/themes/wp-festival-v2.0 git@github.com:DiscoDonniePresents/wp-festival v2.0 --squash
	@git subtree pull --prefix=wp-content/themes/wp-spectacle-chmf git@github.com:DiscoDonniePresents/wp-spectacle-chmf master --squash
	@git subtree pull --prefix=wp-content/themes/wp-spectacle-mbp git@github.com:DiscoDonniePresents/wp-spectacle-mbp master --squash
	@git subtree pull --prefix=wp-content/themes/wp-spectacle-fbt git@github.com:DiscoDonniePresents/wp-spectacle-fbt master --squash
	@git subtree pull --prefix=wp-content/themes/wp-spectacle-isladelsol git@github.com:DiscoDonniePresents/wp-spectacle-isladelsol master --squash

	@echo "Enabled environment variables in .envrc."

# Create MySQL Snapshot
#
#
clean:
	@rm -rf composer.lock
	@rm -rf wp-vendor/composer
	@composer clear-cache
	@echo "Cleared out vendor crap."

##
##
update:
	composer update --no-dev --prefer-dist
	@echo "Updated Composer dependencies."

##
##
flushTransient:
	@echo "Flushing transients."
	@wp --allow-root transient delete-all
	@wp --allow-root db query 'DELETE FROM edm_sitemeta WHERE meta_key LIKE "%_site_transient%"'

## Generate MySQL Snapshot
##
snapshot:
	@make env
	@echo "Creating MySQL snapshot for <${CURRENT_BRANCH}> branch."
	@wp --allow-root db export ${ACCOUNT_NAME}_${CURRENT_BRANCH}.sql
	@gzip ${ACCOUNT_NAME}_${CURRENT_BRANCH}.sql
	@gsutil -m mv ${ACCOUNT_NAME}_${CURRENT_BRANCH}.sql.gz gs://discodonniepresents.com/
	@gsutil -m cp -D gs://discodonniepresents.com/${ACCOUNT_NAME}_${CURRENT_BRANCH}.sql.gz gs://discodonniepresents.com/${ACCOUNT_NAME}_develop.sql.gz
	@echo "Snapshot available at gs://discodonniepresents.com/${ACCOUNT_NAME}_${CURRENT_BRANCH}.sql.gz."

## Create MySQL Snapshot
##
snapshotImport:
	@echo "Downloading MySQL snapshot for <${CURRENT_BRANCH}> branch from from gs://discodonniepresents.com/${ACCOUNT_NAME}_${CURRENT_BRANCH}.sql.gz to ~/tmp/${ACCOUNT_NAME}_${CURRENT_BRANCH}.sql."
	@rm -rf ~/tmp/${ACCOUNT_NAME}_${CURRENT_BRANCH}.sql.gz
	@gsutil -m cp gs://discodonniepresents.com/${ACCOUNT_NAME}_${CURRENT_BRANCH}.sql.gz ~/tmp/${ACCOUNT_NAME}_${CURRENT_BRANCH}.sql.gz
	@gunzip ~/tmp/${ACCOUNT_NAME}_${CURRENT_BRANCH}.sql.gz
	@wp --allow-root db import ~/tmp/${ACCOUNT_NAME}_${CURRENT_BRANCH}.sql
	@rm -rf ~/tmp/${ACCOUNT_NAME}_${CURRENT_BRANCH}.sql
	@wp cache flush
	@wp transient delete-all
	@echo "MySQL snapshot downloaded from gs://discodonniepresents.com/${ACCOUNT_NAME}_${CURRENT_BRANCH}.sql.gz and imported."

# - Import MySQL Snapshot
#
develop:
	@echo "Installing ${CIRCLE_PROJECT_USERNAME}/${CIRCLE_PROJECT_REPONAME}:${CURRENT_TAG} for development."
	@npm install --silent
	@rm -rf composer.lock wp-vendor/composer/installed.json wp-vendor/composer/installers
	@composer update --dev --prefer-source --no-interaction --no-progress
	@make snapshotImport
	@wp option update git:branch ${CURRENT_BRANCH}
	@wp option update git:build ${CIRCLE_BUILD_NUM}
	@wp option update git:organization ${CIRCLE_PROJECT_USERNAME}
	@wp option update git:repository ${CIRCLE_PROJECT_REPONAME}
	@wp transient delete-all
	@wp cache flush

#
#
#
production:
	@echo "Installing ${CIRCLE_PROJECT_USERNAME}/${CIRCLE_PROJECT_REPONAME}:${CURRENT_TAG} for development."
	@wp option update git:branch ${CURRENT_BRANCH}
	@wp option update git:organization ${CIRCLE_PROJECT_USERNAME}
	@wp option update git:repository ${CIRCLE_PROJECT_REPONAME}

#
#
#
storageSync:
	@echo "Pushing storage files from <${STORAGE_DIR}> to <${STORAGE_BUCKET}> bucket."
	$(echo $(wp --allow-root site list --field=url --format=csv) | while read line; do echo "Site: ${item}"; done)

# Prepare for Git Push and push
#
#
release:
	@echo "Running application install ${CIRCLE_PROJECT_USERNAME}/${CIRCLE_PROJECT_REPONAME}:${CURRENT_TAG}."
	@rm -rf composer.lock
	@rm -rf wp-vendor/composer/installed.json
	@rm -rf wp-vendor/composer/installers
	@rm -rf wp-vendor/composer/installers/.gitignore
	@rm -rf wp-vendor/**/.git
	@composer update --optimize-autoloader --no-dev --prefer-dist --no-interaction
	@git add . --all && git commit -m '[ci skip]' && git push

# Prepare for Git Push and push
#
#
snapshotRelease:
	make snapshot
	make release

# Dangerous command. Will dump any local changes.
#
#
reset:
	@echo "Resetting current branch <${CURRENT_BRANCH}> to origin."
	@git fetch --force --quiet origin
	@git clean --force -d --quiet
	@git reset --hard origin/${CURRENT_BRANCH}
	@git pull --force --quiet

#
#
#
merge:
	@echo "Merging current <${CURRENT_BRANCH}> branch with origin/production."
	@git fetch origin
	@git merge --no-ff origin/production -m "Merging with production"

# Actions to be performed after git pull
#
#
post-pull:
	@make varnishPurge

# Purge Varnish.
#
#
varnishPurge:
	@echo "Performing post-pull action."
	@curl -X PURGE discodonniepresents.com
	@curl -X PURGE dayafter.com
	@curl -X PURGE umesouthpadre.com
	@curl -X PURGE somethingwicked.com
	@curl -X PURGE suncitymusicfestival.com

# Install for Staging/Development
#
# - We always dump /wp-vendor/composer/installers* to avoid any issues with installers.
# - Composer install Will delete any unused depds.
# - Composer update will fix anything missing. If "dist" is unavailable, will fall back to source.
#
install:
	@echo "Installing ${CIRCLE_PROJECT_USERNAME}/${CIRCLE_PROJECT_REPONAME}:${CURRENT_TAG}."
	@npm install --silent
	@rm -rf wp-vendor/composer/installed.json wp-vendor/composer/installers
	@composer update --prefer-source --dev --no-interaction --no-progress

# pass commands to Grunt
#
%:
	@npm install
	@grunt $@

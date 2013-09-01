REPORTER = list
LIB_COV = static/lib-cov
JSON_FILE = static/all.json
HTML_FILE = static/coverage.html

test-all: clean document test-code

install:
	cd core && composer.phar install
	cd ux && component install && component build

push:
	cd ux && component build
	git add .
	git commit -m "WIP"
	git push

update-vendor:
	cd core && composer.phar update

update-ux:
	cd ux && component update

build-ux:
	cd ux && component build

document:
	yuidoc --configfile static/yuidoc.json

test-code:
	@NODE_ENV=test mocha \
  --timeout 200 \
  --ui exports \
  --reporter $(REPORTER) \
  test/*.js

clean:
	rm -fr static/assets/*
	rm -fr static/classes/*
	rm -fr static/files/*
	rm -fr static/modules/*
	rm -f static/api.js
	rm -f static/data.json
	rm -f static/index.html
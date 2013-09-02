REPORTER = list
LIB_COV = static/lib-cov
JSON_FILE = static/all.json
HTML_FILE = static/coverage.html

test-all:
	clean
	document
	test-code

install:
	composer.phar install
	component install -d ux
	component build -o ux/build -n app

push:
	git add .
	git commit -m "WIP"
	git push

update:
	composer.phar update
	component install -d ux
	component build -o ux/build -n app
	yuidoc --configfile yuidoc.json

document:
	yuidoc --configfile yuidoc.json

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
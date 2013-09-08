REPORTER = list
LIB_COV = static/lib-cov

test-all: clean document test-code

install:
	upm-install

push:
	yuidoc -q --configfile static/yuidoc.json
	upm-install
	upm-build
	upm-commit

update:
	upm-udpate
	upm-build
	yuidoc -q --configfile static/yuidoc.json

document:
	yuidoc -q --configfile static/yuidoc.json

compile:
	component-install && component-build -o ./ux/build -n app
	lessc   ./ux/styles/content.less ./ux/build/content.css
	lessc   ./ux/styles/content.less ./ux/build/content.min.css -x
	minify  ./ux/build/app.css ./ux/build/app.min.css
	/node_modules/upm/node_modules/uglify-js/bin/uglifyjs ./ux/build/app.js -o ./ux/build/app.min.js

test-code:
	@NODE_ENV=test mocha \
  --timeout 200 \
  --ui exports \
  --reporter $(REPORTER) \
  test/*.js

clean:
	rm -fr static/codex
	rm -fr static/codex/lib-cov
	rm -fr components
	rm -fr ux/build
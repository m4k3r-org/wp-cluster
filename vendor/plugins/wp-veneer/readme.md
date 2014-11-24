***
[![Issues - Bug](https://badge.waffle.io/usabilitydynamics/wp-veneer.png?label=bug&title=Bugs)](http://waffle.io/usabilitydynamics/wp-veneer)
[![Issues - Backlog](https://badge.waffle.io/usabilitydynamics/wp-veneer.png?label=backlog&title=Backlog)](http://waffle.io/usabilitydynamics/wp-veneer/)
[![Issues - Active](https://badge.waffle.io/usabilitydynamics/wp-veneer.png?label=in progress&title=Active)](http://waffle.io/usabilitydynamics/wp-veneer/)
***
[![Dependency Status](https://gemnasium.com/UsabilityDynamics/wp-veneer.svg)](https://gemnasium.com/UsabilityDynamics/wp-veneer)
[![Scrutinizer Quality](http://img.shields.io/scrutinizer/g/UsabilityDynamics/wp-veneer.svg)](https://scrutinizer-ci.com/g/UsabilityDynamics/wp-veneer)
[![Scrutinizer Coverage](http://img.shields.io/scrutinizer/coverage/g/UsabilityDynamics/wp-veneer.svg)](https://scrutinizer-ci.com/g/UsabilityDynamics/wp-veneer)
[![CircleCI](https://circleci.com/gh/UsabilityDynamics/wp-veneer.png?circle-token=08ed40a6f722782ae229774ee7a208a1167b012f)](https://circleci.com/gh/UsabilityDynamics/wp-veneer)
***

## Features
* Varnish Purging
* URL Rewrites
* vElasic Search

## Constants

* WP_THEMES_DIR - Absolute path to storage directory.
* WP_THEME_DIR - Absolute path to theme directory.
* WP_THEME_URL  - URL to themes directory.
* WP_VENEER_STORAGE - Absolute path to storage directory.
* WP_VENEER_PUBLIC  - Absolute path to "public" directory. If multisite is enabled, assets should be organized by apex domains.

## Instance Properties
Properties set by Bootstrap class to the global $wp_veneer object.

* site      - Current site's domain.
* apex      - Current site's apex domain.
* network   - Network's (if applicable) apex domain.
* site_id   - Current site's blog ID.

## W3 Total Cache Issues & Notes
* Verified for seperate Networks: Plugin Activation,
* Ideally "cache" should be located in static/storage/{domain}/cache
* Only single hostname is allowed in settings - stereolivehouston.com images go to direct.nightculture.com
* Files uploaded to "CDN" must be somehow organized by domain.
* Theme files are uploaded into a /system/themes directory. Where does "system" come from?
* The system/.htaccess file.
* Theme file upload only looks for minified files.
* Upgrade Network must be ran after W3 is activated to create tables.
* minified cache broken because URLs go to http://direct.nightculture.com/cache/minify/... while uploads go to /system/minify/... (could be fixed w/ "static" hostname)
* supports_full_page_mirroring is disabled on FTP... why?

## Changelog

### 0.7.1
* Added WP_VENEER_PUBLIC constant to allow explicit setting of directory to be used for uploads.

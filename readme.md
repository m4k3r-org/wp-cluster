[![Issues - Bug](https://badge.waffle.io/usabilitydynamics/wp-crm.png?label=bug&title=Bugs)](http://waffle.io/usabilitydynamics/wp-crm)
[![Issues - Backlog](https://badge.waffle.io/usabilitydynamics/wp-crm.png?label=backlog&title=Backlog)](http://waffle.io/usabilitydynamics/wp-crm/)
[![Scrutinizer Quality](http://img.shields.io/scrutinizer/g/UsabilityDynamics/wp-splash.svg)](https://scrutinizer-ci.com/g/UsabilityDynamics/wp-splash)
[![Scrutinizer Coverage](http://img.shields.io/scrutinizer/coverage/g/UsabilityDynamics/wp-splash.svg)](https://scrutinizer-ci.com/g/UsabilityDynamics/wp-splash)
[![Dependencies](https://gemnasium.com/UsabilityDynamics/wp-splash.svg)](https://gemnasium.com/UsabilityDynamics/wp-splash)


Removed "usabilitydynamics/lib-layout-engine" from composer.json due to failure to install.


### Theme Options
* admin:hide-post-menu
* admin:hide-users-menu
* admin:hide-tools-menu
* admin:hide-comments-menu

### Use to Build:
$ composer install --no-dev

* siteorigin-panels is a "dev" dependency because its not essential for theme to work and SO Panels is a "module" which can be loaded via WordPress as a plugin.* other libraries (lib-model, lib-layout-thing) are disribute in vendor directry

### Scrolling Magic

* scrollReveal - Allows easy DOM bindings with data-scroll-reveal attribute and "after 2s, ease-in 32px and reset over .66s"
* skrollr - More advanced scroll effect handler.

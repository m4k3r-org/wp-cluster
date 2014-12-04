### Getting Started

* Create a mobile site, e.g. http://ios.mobile.discodonniepresents.com/
* The site


### Idea
* Setup a special site on the network (ios.mobile.discodonniepresents.com).
* This site is used to setup pages, URL structure, etc. and to configure mobile options.
* A mobile-friendly theme is used to generate the frame.
* Include various utility scripts, such as FastClick, https://github.com/cubiq/iscroll, http://zeptojs.com/#touch
* PageSpeed is used to optimize the output for PhoneGap.
* We use https://wordpress.org/plugins/staticpress/ to iterate over site and generate output for PhoneGap.
* The PhoneGap build process starts with a utility fetching the site's sitemap and downloading static pages into Cordova folder.
* The config.xml file can also be generated during this step.


### Plugins to Try
gs://discodonniepresents.com/ddp_production.sql.gz.

### Benefits to Workflow
* We can use PHP and WordPress API to generate things that would otherwise be static pages. 
* Automatically build sprites of images.
* We can leverage PageSpeed to prepare final output before static pages are created.
* We can generate models at the time of release and package them as JSON files.  
* The site can be viewed in browser with some logic adding support to emulate PhoneGap. (https://github.com/makesites/phonegap-shim)

* Kitchen Sink: https://github.com/jcfischer/pgkitchensink
* Sample site: http://jxp.github.io/phonegap-desktop/demo/
* Tutorial: http://coenraets.org/blog/phonegap-tutorial/


### Structure
* Festivals
* Events
* Media
* News & Social
* Discover
* Invite


#### Search
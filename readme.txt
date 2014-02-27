=== Global Javascript ===

Contributors: psmagicman, ctlt-dev, ubcdev, enej
Donate link:
Tags: plugin, javascript, editor, ubc, appearance, global, js, dynamic, ACE, minify, code, custom, jquery, backbone, thickbox, modernizr, json, underscore, script, dependency
Requires at least: WordPress 3.5 and PHP 5.2.1
Tested up to: 3.5
Stable tag: 1.0
License: GNU General Public License
License URI: http://www.gnu.org/licenses/gpl-3.0.html

A simple Javascript writing/editing tool using ACE editor and the Minify library


== Description ==

Allows the user to create custom javascript for their Wordpress powered site on a global level.

The Javascript made by the user of the plugin will be loaded after any other Javascript that is used by other plugins and/or themes.

PLUGIN FEATURES:

Some of the features that are included in this plugin are:

- syntax highlighting

- code minification

- revisions

- grouping blocks of code

- including dependencies (libraries such as jQuery and backbone.js)

- elegant editor interface courtesy of ACE

FUTURE ADDITIONS:

Here are some of the things that I will be adding sometime in the future as the plugin grows :) :

- ability to choose your minifier of choice from the minify library

- ability to load different javascript files for each page/post as decided by the plugin user

- ability to save additional javascript files and create tabs to organize the files

- a separate button to completely delete the javascript files from the server

== Installation ==

1. Upload 'global-javascript' folder to the '/wp_content/plugins' directory. Or alternatively install through the WordPress admin dashboard.
2. Activate the plugin through the 'Plugins' menu in the WordPress admin dashboard.
3. Navigate to the 'Appearance' tab in the WordPress admin dashboard sidebar.
4. Click on the link titled 'Global Javascript'

== Frequently Asked Questions ==

= What does the plugin do? =

The plugin is just a simple Javascript writing/editing tool that allows the user to create some custom Javascript to their WordPress powered website.

= How much experience do I need to use this plugin? =

Javascript is not too difficult to pick up. There are many freely available resources online that you can find to help you get started. The site that I used to get started on Javascript is http://w3schools.com/js/default.asp

= Does it do anything to the database? =

Yes. This plugin allows the use of revisions and will add entries to the database in the posts table. The database is accessed using WordPress and PHP core functions and there are no direct SQL queries.

= Does the plugin access the filesystem? =

Yes. This plugin will save and delete files to the filesystem. The files will be located in the uploads folder under the sub-folder "global-js" on a default WordPress installation.

= Does the plugin work in a multisite configuration? =

Yes. The plugin works in a multisite environment as it creates files and folders in the site's own portion of the uploads directory.

= How do I get rid of the javascript files from the server? =

At the moment, the only way to do this is to save a blank javascript file. This will delete all the relevant files from your uploads folder. There is currently no way to delete it from your database posts table through the plugin. You will have to access the database and delete it manually. You can find the posts in the data base by searching for post_title = 'Global JavaScript Editor' in the posts table.

== Screenshots ==

1. Screenshot of the editor itself.

2. Screenshot of where to find the editor in the dashboard.

3. Another screenshot of where to find the editor in the dashboard.

4. A screenshot of the minified code.

== Changelog ==

v.1.0 - release version

v0.16 - dependencies such as jQuery are now included in the plugin
      - the current libraries can be included by ticking the checkbox in the editor
      - included a function call to clear cache on save if supercache is enabled

v0.15 - plugin now minifies the javascript using the Google Closure Compiler at simple settings included in the PHP minifier library
        * Julien's note: if you want to change which minifier to use go to the filter function in the code
      - changed the way files are saved and loaded to account for the extra time to minify the javascript

v0.14 - plugin no longer does anything on activation and deactivation (not necessary to)
      - no longer creates a separate directory
      - changed the way older files are deleted

v0.13 - changed the behavior of the deactivate function. (does not depend on the host being UNIX based)
      - added a recursive function that handles removing directories and the contents inside the directories

v0.12 - added 2 additional hooks that will add and delete directories upon activation or deactivation respectively
      - defined the uninstall function though at the moment nothing calls it (need to do additional testing and research before enabling it)

v0.11.1 - added a conditional to only load the javascript if the files exist

v0.11 - added a conditional to check if WP_DEBUG is set to true
        * if WP_DEBUG is true plugin will load the non stripped version of the javascript
        * no other additional changes to the plugin if in debug mode

v0.10.3 - cleaned up some of the code as well as changing the editor name to be more descriptive and similar to plugin name
        - fixed some typos that caused javascript to not load properly

v0.10.2 - fixed a bug where previously uploaded files are not deleted

v0.10.1 - fixed a bug where single line comments were not being replaced
        - plugin now saves external javascript to a custom directory in uploads by site and creates one if not available

v0.10 - added a regex replacer to prepare for minification in next release
      - added additional saving that utilizes unix timestamping to prep for future loading method

v0.9.2 - changed the behaviour of the Javascript saving and loading

v0.9.1 - fixed a bug where files were not being saved properly using the new method
       - fixed a bug where the redirect was giving a permissions error

v0.9 - changed the way javascript is saved
        * previous method of saving was producing unintended results
        * new method still requires some testing on multisites
        * new method involves the use of the wp_filesystem
        * codex.wordpress.org/Filesystem_API
     - changed the way the paths work
        * this new method should work regardless of what the parent directory is called

v0.8 - changed the way javascript is injected to the page 
        * javascript is now injected from an external file
     - javascript is now saved to an external javascript file as well
     - current handling of saving needs to be updated; may be security issues

v0.7 - changed the code to a class instead

v0.6.1 - fixed some typos

v0.6 -  updated default text and info for global-javascript.php

v0.5 -  added stylesheets to the editor
     -  changed the names of some style class tags inside codemirror.js 

v0.4 -  codemirror javascript mode code should have more meaning now

v0.3 -  beautified the codemirror javascript mode code

v0.2 -  changed code to edit javascript instead of css

v0.1 -  core code created from the improved simpler css plugin by CTLT

== Acknowledgements ==

This plugin uses the minify library package by Steve Clay (steve@mrclay.org) and Ryan Grove (ryan@wonko.com).
More info at http://code.google.com/p/minify/

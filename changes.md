#### 2.3.0
* Convert all database tables to MyISAM.
* Convert all database tables to use utf8_general_ci character set.
* Removed unused Grunt/Node modules, simplified Gruntfile.js.
* Removed local-debug.php handling, should use environment variables.
* Removed support for ENVIRONMENT variable, expected to use WP_ENV and/or PHP_ENV.
* Re-created new ORPHAN develop branch from develop-refactor.
* (WIP) Add wp-comment-post.php rewrite for posting comments instead of symlinking file.
* (WIP) Added standard "save site" method to fix common issues such as upload paths/urls, transient paths, etc.
* (WIP) Revert application structure to native-supported naming conventions, fixes problems with missing themes.
* (WIP) Removed Grunt tasks in lieu of WP-CLI commands.

#### 2.2.1
* Disabled PageSpeed and NewRelic on admin.

#### 2.2.0
* Moved MU plugins to be loaded from ./application
* Added autoload.php MU plugin for loading vendor libs
* Re-installed wp-cluster, wp-veneer and wp-network as valid plugins, removing ./vendor/modules completely.
* Removed automatic loading of wp-vertical-edm and wp-elastic, should be enabled as a plugin.
* Reinstalled plugins from GitHub: wp-github-updater, wp-network, wp-veneer, wp-cluster, wp-elastic, wp-event-post-type-v0.5, wp-pagespeed and wp-revisr.
* Setup automatic plugin activation for required plugins.
* Fixed commenting with wp-comments-post.php
* Re-installed wp-splash. [wp theme enable wp-splash --network]
* Replaced wp-spectacle with wp-spectacle-v1.0 [wp theme enable wp-spectacle-v1.0 --network]
* Replaced wp-spectacle-2 with wp-spectacle-v2.0 [wp theme enable wp-spectable-v2.0 --network]
* Replaced wp-disco with wp-disco-v2.0 [wp theme activate wp-disco-v2.0].
* Copy wp-disco settings [wp option get theme_mods_wp-disco --format=json | wp option update theme_mods_wp-disco-v2.0 --format=json]
* Replaced flawless with wp-flawless-v1.0 [wp theme enable wp-flawless-v1.0 --network]
* Replaced wp-festival with wp-festival-v1.0 [wp theme enable wp-festival-v1.0 --network]
* Replaced wp-festival-2 with wp-festival-v2.0 [wp theme enable wp-festival-v2.0 --network]
* Added "wp utility" command for seeing useful lists such as cross-network active theme.
* Fixed "lib-settings" and "lib-utility" deps to have fixed versions.
* Added "wp utility dns" to see list of sites and the resolved IPs.
* Created post-deployment script [https://gist.github.com/andypotanin/648f2ba3e6acada05b1d]

#### 2.1.3
* Added changes.md
* Added "make snapshotImport" command to download MySQL snapshot from RDS.
* Installed latest version of wp-github-updater.
* Added GitHub Sync MU plugin.
* Updated GravityForms, WPML and GitHub Updater.

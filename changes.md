#### 2.2.0
* Moved MU plugins to be loaded from ./application
* Added autoload.php MU plugin for loading vendor libs
* Re-installed wp-cluster, wp-veneer and wp-network as valid plugins, removing ./vendor/modules completely.
* Removed automatic loading of wp-vertical-edm and wp-elastic, should be enabled as a plugin.
* Reinstalled plugins from GitHub: wp-github-updater, wp-network, wp-veneer, wp-cluster, wp-elastic, wp-event-post-type-v0.5, wp-pagespeed and wp-revisr.
* Setup automatic plugin activation for required plugins.
* Fixed commenting with wp-comments-post.php
* Replaced wp-spectacle with wp-spectable-v1.0
* Replaced wp-spectacle-2 with wp-spectable-v2.0
* Replaced wp-disco with wp-disco-v2.0
* Replaced flawless with wp-flawless-v1.0
* Replaced wp-festival with wp-festival-v1.0
* Replaced wp-festival-2 with wp-festival-v2.0

#### 2.1.3
* Added changes.md
* Added "make snapshotImport" command to download MySQL snapshot from RDS.
* Installed latest version of wp-github-updater.
* Added GitHub Sync MU plugin.
* Updated GravityForms, WPML and GitHub Updater.

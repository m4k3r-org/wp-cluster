#### 2.2.0
* Moved MU plugins to be loaded from ./application
* Added autoload.php MU plugin for loading vendor libs
* Re-installed wp-cluster, wp-veneer and wp-network as valid plugins, removing ./vendor/modules completely.
* Removed automatic loading of wp-vertical-edm and wp-elastic, should be enabled as a plugin.

#### 2.1.3
* Added changes.md
* Added "make snapshotImport" command to download MySQL snapshot from RDS.
* Installed latest version of wp-github-updater.
* Added GitHub Sync MU plugin.
* Updated GravityForms, WPML and GitHub Updater.

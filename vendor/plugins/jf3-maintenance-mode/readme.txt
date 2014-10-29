=== Plugin Name ===
Contributors: jfinch3
Donate link: http://wordpress.org/
Tags: developer, maintenance
Requires at least: 2.9.2
Tested up to: 3.5.1
Stable tag: 1.3


This plugin allows you to specify a maintenance mode message / page for your site and configure users to bypass the maintenance mode functionality.

== Description ==

This plugin is intended primarily for designers / developers that need to allow clients to preview sites 
before being available to the general public or to temporarily hide your WordPress site while undergoing 
major updates.

Any logged in user with WordPress administrator privileges will be allowed to view the site regardless 
of the settings in the plugin

The behavior of this can be enabled or disabled at any time without loosing any of settings configured in 
it's settings pane. However, deactivating the plugin is recommended versus having it activated while 
disabled.

A list if IP addresses can be setup to completely bypass maintenance mode. This option is useful when 
needing to allow a client's entire office to access the site while in maintenance mode without needing to
maintain individual access keys.

Access keys work by creating a key on the user's computer that will be checked against when maintenance
mode is active. When a new key is created, a link to create the access key cookie will be emailed to the
email address provided. Access can then be revoked either by disabling or deleting the key.

This plugin allows three (3) methods of notifying users that a site is undergoing maintenance:

  1. They can be presented with a message on a page created by information entered into the plugin settings
     pane.

  2. They can be presented with a custom HMTL page 

  3. They can be redirected to a static page. This static page will need to be uploded to the server via
     FTP or some other method. This plugin DOES NOT include any way to upload the static page file.

== Installation ==

1. Upload the `jf3-maintenance-mode` folder to the `/wp-content/plugins/` directory.

2. Activate the plugin through the `Plugins` menu in WordPress.

3. Configure the settings through the `JF Maint Redir` settings panel.

== Frequently Asked Questions ==

= Why don't you have any FAQs =

We haven't received any questions yet.

== Screenshots ==

1. Settings Panel Screenshot

== Changelog ==

= 1.3 =
Updated to return 503 header when enabled to prevent indexing of maintenance page. 

Also, wildcards are allowed to enable entire class C ranges. Ex: 10.10.10.*

A fix affecting some installations using a static page has been added. Thanks to Dorthe Luebbert.

= 1.2 =
Fixed bug in Unrestricted IP table creation.

= 1.1 =
Updated settings panel issue in WordPress 3.0 and moved folder structure to work properly with WordPress auto install.

= 1.0 =
First release. No Changes Yet.

== Upgrade Notice ==

= 1.2 =
To upgrade completely, deactivate & then re-activate the plugin after performing the upgrade.A

= 1.1 =
All files have been moved into the root to accomodate the fact that WordPress made it's own folder for the plugin files. I have not created an auto upgrade feature at this point. If you have issues, just delete the `wpjf3_maintenance_redirect` from version 1.0. This version lives in the `jf3-maintenance-mode` folder as determined by WordPress.org.

= 1.0 =
This is the first relase.


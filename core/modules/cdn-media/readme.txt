=== Amazon S3 Uploads ===
Tags: Media, Amazon, S3, CDN, Admin, Uploads, Mirror
Contributors: atvdev
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7T88Q3EHGD9RS
Requires at least: 3.5.2
Tested up to: 3.5.2
Stable tag: 1.9.4
License: GPLv2.1

Moves your uploads to Amazon S3 via cron jobs.

== Description ==
Hello,
Amazon S3 Uploads does exactly what you ask, it moves all your uploads to Amazon S3, deletes the uploaded files from your server and every time that a file is requested, it automatically redirects to the file that is on Amazon S3.

Be aware that files from your server are uploaded to the Amazon S3 via cron job and not immediately. So you will have to wait some minutes (half hour) to see results.

Cheers!


REQUIREMENTS

*   Wordpress 3.5.2+
*   PHP 5.3.3+ compiled with the cURL extension
*   A recent version of cURL 7.16.2+ compiled with OpenSSL and zlib
*   "AllowOverride All" in your apache configuration


TERMS OF USE

*   The developer of this plugin is not held responsible for any file loss or and damage due to this plugin.
*   It may so happen that this plugin may be discontinued at any point. The developer of the plugin will not be held responsible for that.

== Installation ==
1. Copy plugin files to wordpress wp-content/plugins folder
3. Activate the plugin
4. Goto 'Amazon S3 Uploads' page under plugins and set up your Amazon S3 credentials
5. The plugin will not work until all the configs are completed

== Frequently Asked Questions ==

= How this plugin affects my blog? =
Any file in the uploads directory will be moved to Amazon S3 with a relative path to it's current. Any uploaded file requested, if not present in the uploads directory, will be redirected to Amazon S3. Your uploads urls remain without any change.

= What happens if I choose to disable this plugin. Does my blog now have NO images? Or does it just not continue the cronjob in the future? =
If you turn off the plugin, the cron job will stop, images that are already on the Amazon S3 remain there and are streamed from Amazon, new images that you will upload will be stored on your server and streamed from your server.

= Should I modify any code in wordpress? =
Not needed.

= Can I manage my files in Amazon S3? =
No. You cannot manage the files in Amazon S3 using this plugin.

= What should I do if I want to stop using Amazon S3? =
If you want all your files to be again on your server, you will have to manually download them from Amazon S3 selected bucket (and subfolder if applied) to your uploads folder. Folders on your server and on Amazon S3 are maintained the same eg.
/http://example.com/wp-content/uploads/2011/10/some_file.jpg
on Amazon S3 it is stored
{amazon_bucket_name}/{chosen_subdirectory}/2011/10/some_file.jpg

= How can I setup advanced configuration for MultiSite? =
Sorry, but you can not. Maybe in a future release.

== Changelog ==

= 1.9.1 =
* General plugin rewrite

= 1.09 =
* Url encoding bug fixed
* Delete attachment fixed
* Ability to exclude filetypes

= 1.05 - 1.08 =
* Minor bug fixes

= 1.04 =
* MultiSite complex configuration added
* Hidden secret key
* Revised endpoint finding function
* Split the plugin into several classes
* File searching without the db
* Added MultiSite config sample

= 1.03 =
* MultiSite support added

= 1.02 =
* Upgraded Amazon S3 php class
* Fixed SSL/HTTPS issue

= 1.01 =
* First version of the plugin

== Upgrade Notice ==

= 1.9.1 =
General plugin rewrite

= 1.09 =
Oh, some goodies packed in this one.

= 1.05 - 1.08 =
Minor bug fixes

= 1.04 =
This version incorporates some fresh holy Earth's lightnings tuned by mighty Zeus himself.

= 1.03 =
MultiSite support added.

= 1.02 =
This version fixes Amazon S3 bucket region bug and new Amazon S3 php class is included.

= 1.01 =
First version of the plugin.

== Screenshots ==
* No screenshots available yet.

== Terms of use ==
* The developer of this plugin is not held responsible for any file loss or and damage due to this plugin.
* It may so happen that this plugin may be discontinued at any point. The developer of the plugin will not be held responsible for that.

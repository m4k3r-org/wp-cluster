#### 0.7.0
* Moved .htaccess generation into admin_init only.
* Improved toolbar Server Name, Git Branch and Server IP display.
* Site post-creation/post-edit action to modify upload_url_path and upload_path options. (WIP)
* Removed esoteric constants: SUNRISE_LOADED, DOMAIN_MAPPING, ENVIRONMENT, WP_BASE_URL, WP_BASE_DOMAIN
* Removed WP_BASE_DIR constant since its illogical - should either use ABSPATH, WP_CONTENT_DIR, MUPLUGINDIR, WP_PLUGIN_DIR, WP_LANG_DIR, etc.
* Added support for WP_VENEER_VARNISH_IP to set where to send purge requests.
* Made WP_LOGS_DIR be relative to WP_CONTENT_DIR.
* Made WP_DEBUG and WP_DEBUG_DISPLAY be configurable via Apache environment even if not defiend in composer.json
* Bundled in monolog/monolog and removed Raygun4php\RaygunClient dependency.
* Added new options: varnish.enabled, api.enabled, logs.enabled and rewrites.enabled. Disabled by default.
* (WIP) Set WP_LANG_DIR properly.
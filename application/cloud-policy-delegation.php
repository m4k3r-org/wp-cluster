<?php
/**
 * Plugin Name: Policy Delegation
 * Plugin URI: http://usabilitydynamics.com/plugins/
 * Description: Main plugin to handle all site specific bootstrap tasks
 * Author: Usability Dynamics, Inc.
 * Version: 0.1.2
 * Author URI: http://usabilitydynamics.com
 *
 * ### Cloud Options
 * Settings that apply to entire cloud/cluster of sites.
 *
 * ### Network (Site) Options
 * Stored in "site-options" cache group using site_id prefix, otherwise fetched from sitemeta table.
 *
 * * pre_site_option_{key} - Ran before making DB call and checking cache.
 * * default_site_option_{key} - Ran after have value
 * * site_option_{key} - Ran before making DB call and checking cache.
 *
 * ### Site (Blog) Options
 * Settings pertaining to a single site.
 *
 * * pre_option_{key} - Ran before making DB call and checking cache.
 * * default_option_{key}
 * * option_{key}
 *
 * ### Transients
 * Transients check to see if external cache is being used before checking in options/sitemeta tables.
 *
 * * pre_transient_{$key} | pre_site_transient_{$key} - Called before cache/DB check.
 * * transient_{$key} | site_transient_{$key} - Applies after cache/DB check.
 *
 * ### General Notes
 * * The pre_ filters are applied when WP_SETUP_CONFIG constant is set.
 *
 */
namespace wpCloud\PolicyDelegation {

	add_filter( 'default_site_option_active_sitewide_plugins', function ( $default ) {
		return $default;
	} );

	add_filter( 'default_site_option_siteurl', function ( $default ) {
		return $default;
	} );

	add_filter( 'default_site_option_subdomain_install', '__return_true' );
	add_filter( 'default_site_option_global_terms_enabled', '__return_true' );
	add_filter( 'default_site_option_add_new_users', '__return_true' );

	add_filter( 'default_site_option_ms_files_rewriting', '__return_null' );
	add_filter( 'default_site_option_site_admins', '__return_null' );
	add_filter( 'default_site_option_illegal_names', '__return_null' );

	add_filter( 'pre_option_active_plugins', function () {

	} );

	add_filter( 'pre_option_recently_edited', function () {

	} );

	add_filter( 'pre_option_template', function () {

	} );

	add_filter( 'pre_option_stylesheet', function () {

	} );

	add_filter( 'pre_option_upload_path', function () {

	} );

	// URL without a trailing slash.
	add_filter( 'pre_option_upload_url_path', function () {

	} );

	// https://gist.githubusercontent.com/andypotanin/2de82e5d6502cc92a654/raw/_transient_plugin_slugs
	add_filter( 'pre_site_transient_plugin_slugs', function () {

	} );

	// https://gist.githubusercontent.com/andypotanin/2de82e5d6502cc92a654/raw/recently_activated
	add_filter( 'pre_option_recently_activated', '__return_null' );
	add_filter( 'pre_option_theme_switched', '__return_null' );
	add_filter( 'pre_option_allowedthemes', '__return_null' );
	add_filter( 'pre_option_allowed_themes', '__return_null' );

}
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
 *
 * * can_compress_scripts - Should be disabled if PageSpeed is available.
 *
 */
namespace EDM\Application\Policy {

	// http://discodonniepresents.com/api/v1/site.json
	add_action( 'wp_ajax_/v1/site', 'EDM\Application\Policy\api_site' );
	add_action( 'wp_ajax_nopriv_/v1/site', 'EDM\Application\Policy\api_site' );

	add_filter( 'pre_site_option_active_sitewide_plugins', '__return_false' );
	add_filter( 'pre_site_option_siteurl', '__return_false' );
	add_filter( 'pre_site_option_global_terms_enabled', '__return_true' );
	add_filter( 'pre_site_option_add_new_users', '__return_true' );
	add_filter( 'pre_site_option_ms_files_rewriting', '__return_zero' );
	add_filter( 'pre_site_option_can_compress_scripts', '__return_zero' );

	add_filter( 'pre_site_option_subdomain_install', '__return_true' );
	add_filter( 'pre_site_option_site_admins', '__return_false' );
	add_filter( 'pre_site_option_illegal_names', '__return_false' );

	add_filter( 'pre_option_recently_edited', '__return_false' );
	add_filter( 'pre_option_template', '__return_false' );
	add_filter( 'pre_option_stylesheet', '__return_false' );
	add_filter( 'pre_option_active_plugins', '__return_false' );
	add_filter( 'pre_option_upload_path', '__return_false' );
	add_filter( 'pre_option_current_theme', '__return_false' );

	// URL without a trailing slash.
	add_filter( 'pre_option_upload_url_path', '__return_false' );

	// https://gist.githubusercontent.com/andypotanin/2de82e5d6502cc92a654/raw/_transient_plugin_slugs
	add_filter( 'pre_site_transient_plugin_slugs', '__return_false' );
	add_filter( 'pre_site_transient_update_themes', '__return_false' );
	add_filter( 'pre_site_transient_theme_roots', '__return_false' );
	add_filter( 'pre_site_transient_timeout_theme_roots', '__return_false' );

	add_filter( 'pre_option_blog_public', '__return_true' );
	add_filter( 'pre_option_uploads_use_yearmonth_folders', '__return_true' );

	// https://gist.githubusercontent.com/andypotanin/2de82e5d6502cc92a654/raw/recently_activated
	add_filter( 'pre_option_recently_activated', '__return_false' );
	add_filter( 'pre_option_theme_switched', '__return_false' );
	add_filter( 'pre_option_allowedthemes', '__return_false' );

	// Site/Network change detection.
	add_action( 'update_option_home', 'EDM\Application\Policy\site_changed' );
	add_action( 'update_option_siteurl', 'EDM\Application\Policy\site_changed' );
	add_filter( 'upload_dir', 'EDM\Application\Policy\upload_dir' );

	function api_site() {

		wp_send_json( array(
			"siteurl"                       => get_option( 'siteurl' ),
			"network.url"                   => get_site_option( 'siteurl' ),
			"home"                          => get_option( 'home' ),
			"current_theme"                 => get_option( 'current_theme' ),
			"stylesheet"                    => get_option( 'stylesheet' ),
			"template"                      => get_option( 'template' ),
			"theme_roots"                   => get_site_transient( 'theme_roots' ),
			"active_plugins"                => get_option( 'active_plugins' ),
			"site_admins"                   => get_site_option( 'site_admins' ),
			"active_sitewide_plugins"       => get_site_option( 'active_sitewide_plugins' ),
			"can_compress_scripts"          => get_site_option( 'can_compress_scripts' ),
			"ms_files_rewriting"            => get_site_option( 'ms_files_rewriting' ),
			"theme_switched"                => get_option( 'theme_switched' ),
			"blog_public"                   => get_option( 'blog_public' ),
			"recently_edited"               => get_option( 'recently_edited' ),
			"upload"                        => wp_upload_dir(),
			"gcs_sync_bucket"               => get_option( '_gcs_sync_bucket' ),
			"gcs_sync_enabled"              => get_option( '_gcs_sync_enabled' ),
			"upload_path"                   => get_option( 'upload_path' ),
			"upload_url_path"               => get_option( 'upload_url_path' ),
			"allowedthemes"                 => get_option( 'allowedthemes' ),
			// "update_plugins"                => get_site_transient( 'update_plugins' ),
			// "update_themes"                 => get_site_transient( 'update_themes' ),
			// "update_core"                   => get_site_transient( 'update_core' ),
			// "uploads_use_yearmonth_folders" => get_option( 'uploads_use_yearmonth_folders' ),
			// "subdomain_install"             => get_site_option( 'subdomain_install' ),
			// "global_terms_enabled"          => get_site_option( 'global_terms_enabled' ),
			// "thumbnail_crop"                => get_option( 'thumbnail_crop' ),
			// "thumbnail_size_w"              => get_option( 'thumbnail_size_w' ),
			// "thumbnail_size_h"              => get_option( 'thumbnail_size_h' ),
			// "medium_size_w"                 => get_option( 'medium_size_w' ),
			// "medium_size_h"                 => get_option( 'medium_size_h' ),
			// "large_size_w"                  => get_option( 'large_size_w' ),
			// "large_size_h"                  => get_option( 'large_size_h' ),
		));

	}

	function upload_dir( $settings ) {
		return $settings;
	}

	function site_changed( $old_value, $value ) {

	}

}
<?php
/**
 * Plugin Name: Vendor Autoload
 * Plugin URI: http://usabilitydynamics.com/
 * Description: Load composer stuff.
 * Author: Usability Dynamics, Inc.
 * Version: 0.1
 * Author URI: http://usabilitydynamics.com
 *
 * @source https://github.com/wemakecustom/wp-mu-composer
 *
 */
namespace EDM\Application {

	if ( ! defined( 'WP_VENDOR_LIBRARY_DIR' ) ) {
		define( 'WP_VENDOR_LIBRARY_DIR', ABSPATH . 'wp-vendor/' );
	}

	if ( ! defined( 'WP_VENDOR_AUTOLOAD_PATH' ) ) {
		define( 'WP_VENDOR_AUTOLOAD_PATH', WP_VENDOR_LIBRARY_DIR . '/autoload.php' );
	}

	// We don't autoload if we are currently in CLI mode because it'll cause a class conflict. Let's see if we can work around this.
	if ( ! defined( 'WP_CLI' ) && file_exists( WP_VENDOR_AUTOLOAD_PATH ) ) {
		require_once( WP_VENDOR_AUTOLOAD_PATH );
	}

	// @note Gotta do this BEFORE plugins are activated, or else wp-elastic won't recognize schemas..
	if ( defined( 'WP_PLUGIN_DIR' ) && ( isset( $current_blog ) && $current_blog->domain == 'discodonniepresents.com' ) && is_dir( WP_PLUGIN_DIR . '/wp-vertical-edm/static/schemas' ) ) {
		define( 'WP_ELASTIC_SCHEMAS_DIR', WP_PLUGIN_DIR . '/wp-vertical-edm/static/schemas' );
	}

	/** Init the application */
	if ( class_exists( 'EDM\Application\Bootstrap' ) ) {
		new Bootstrap;
	}

	add_action( 'init', function () {
	});

	/**
	 *
	 * The methods "is_plugin_active" and "activate_plugin" are only available on control panel.
	 *
	 */

	add_action( 'admin_init', function () {

		// No pagespeed on backend
		if ( ! headers_sent() ) {
			header( 'PageSpeed:off' );
		}

		// Don't run newrelic stuff on backend.
		if ( function_exists( 'newrelic_ignore_transaction' ) ) {
			newrelic_ignore_transaction();
		}

		if ( ! is_plugin_active( 'wpmandrill/wpmandrill.php' ) ) {
			activate_plugin( 'wpmandrill/wpmandrill.php', null, true );
		}

		if ( ! is_plugin_active( 'wp-veneer/wp-veneer.php' ) ) {
			activate_plugin( 'wp-veneer/wp-veneer.php', null, true );
		}

		if ( ! is_plugin_active( 'wp-cluster/wp-cluster.php' ) ) {
			activate_plugin( 'wp-cluster/wp-cluster.php', null, true );
		}

		if ( ! is_plugin_active( 'wp-network/wp-network.php' ) ) {
			activate_plugin( 'wp-network/wp-network.php', null, true );
		}

		if ( ! is_plugin_active( 'wp-vertical-edm/vertical-edm' ) ) {
			activate_plugin( 'wp-vertical-edm/vertical-edm.php', null, true );
		}

		if ( ! is_plugin_active( 'wp-github-updater/github-updater' ) ) {
			activate_plugin( 'wp-github-updater/github-updater.php', null, true );
		}

		if ( ! is_plugin_active( 'wp-event-post-type-v0.5/wp-event-post-type.php' ) ) {
			activate_plugin( 'wp-event-post-type-v0.5/wp-event-post-type.php', null, true );
		}

		if ( ! is_plugin_active( 'wp-elastic/wp-elastic.php' ) ) {
			activate_plugin( 'wp-elastic/wp-elastic.php', null, true );
		}

	} );

	add_action( 'plugins_loaded', function () {
		global $wp_veneer, $current_blog;

		if ( isset( $wp_veneer ) && method_exists( $wp_veneer, 'set' ) ) {
			//$wp_veneer->set( 'rewrites.login', true );
			//$wp_veneer->set( 'rewrites.manage', true );
			//$wp_veneer->set( 'rewrites.api', true );

			$wp_veneer->set( 'static.enabled', true );
			$wp_veneer->set( 'cdn.enabled', true );
			$wp_veneer->set( 'cache.enabled', true );

			$wp_veneer->set( 'media.shard.enabled', false );
			$wp_veneer->set( 'scripts.shard.enabled', false );
			$wp_veneer->set( 'styles.shard.enabled', false );
		}

	} );

	/**
	 * Some quick hackish WPML fixes
	 */
	function wpml_shortcode_func() {
		do_action( 'icl_language_selector' );
	}

	if ( function_exists( 'add_shortcode' ) ) {
		add_shortcode( 'wpml_lang_selector', 'wpml_shortcode_func' );
	}

	ob_start( function ( $buffer ) {
		//$buffer = str_replace( '/wp-login.php', '/manage/login', $buffer );
		//$buffer = str_replace( '/wp-admin/', '/manage/', $buffer );
		return $buffer;
	} );

}
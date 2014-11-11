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

	use wpCloud\Vertical;

	if ( ! defined( 'WP_VENDOR_AUTOLOAD_PATH' ) ) {
		define( 'WP_VENDOR_AUTOLOAD_PATH', '/autoload.php' );
	}

	// We don't autoload if we are currently in CLI mode because it'll cause a class conflict. Let's see if we can work around this.
	if ( ! defined( 'WP_CLI' ) && file_exists( ABSPATH . WP_VENDOR_AUTOLOAD_PATH ) ) {
		require_once( ABSPATH . WP_VENDOR_AUTOLOAD_PATH );
	}

	/** Init the application */
	if ( class_exists( 'EDM\Application\Bootstrap' ) ) {
		new Bootstrap;
	}

	/**
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

	});

	add_action( 'init', function () {
		global $current_blog;

		if( class_exists( '\wpCloud\Vertical\EDM\Bootstrap' ) &&  isset( $current_blog ) && $current_blog->domain == 'discodonniepresents.com' ) {
			Vertical\EDM\Bootstrap::loadModel( WP_PLUGIN_DIR . '/wp-vertical-edm/static/schemas/artist.json' );
			Vertical\EDM\Bootstrap::loadModel( WP_PLUGIN_DIR . '/wp-vertical-edm/static/schemas/credit.json' );
			Vertical\EDM\Bootstrap::loadModel( WP_PLUGIN_DIR . '/wp-vertical-edm/static/schemas/event.json' );
			Vertical\EDM\Bootstrap::loadModel( WP_PLUGIN_DIR . '/wp-vertical-edm/static/schemas/imageGallery.json' );
			Vertical\EDM\Bootstrap::loadModel( WP_PLUGIN_DIR . '/wp-vertical-edm/static/schemas/post.json' );
			Vertical\EDM\Bootstrap::loadModel( WP_PLUGIN_DIR . '/wp-vertical-edm/static/schemas/promoter.json' );
			Vertical\EDM\Bootstrap::loadModel( WP_PLUGIN_DIR . '/wp-vertical-edm/static/schemas/tour.json' );
			Vertical\EDM\Bootstrap::loadModel( WP_PLUGIN_DIR . '/wp-vertical-edm/static/schemas/venue.json' );
			Vertical\EDM\Bootstrap::loadModel( WP_PLUGIN_DIR . '/wp-vertical-edm/static/schemas/videoObject.json' );
		}

	});

	add_action( 'plugins_loaded', function () {
		global $wp_veneer, $wp_cluster, $wp_pagespeed;

		if ( isset( $wp_pagespeed ) && method_exists( $wp_pagespeed, 'set' ) ) {
			$wp_pagespeed->set( 'core.enabled', true );
			$wp_pagespeed->set( 'minify.enabled', false );
		}

		if ( isset( $wp_cluster ) && method_exists( $wp_cluster, 'set' ) ) {
			$wp_cluster->set( 'toolbar.git.enabled', true );
		}

		if ( isset( $wp_veneer ) && method_exists( $wp_veneer, 'set' ) ) {

			$wp_veneer->set( 'rewrites.login', false );
			$wp_veneer->set( 'rewrites.manage', false );
			$wp_veneer->set( 'rewrites.api', false );

			$wp_veneer->set( 'static.enabled', false );
			$wp_veneer->set( 'cdn.enabled', false );
			$wp_veneer->set( 'cache.enabled', false );

			$wp_veneer->set( 'media.shard.enabled', false );
			$wp_veneer->set( 'assets.shard.enabled', false );
			$wp_veneer->set( 'scripts.shard.enabled', false );
			$wp_veneer->set( 'styles.shard.enabled', false );
		}

	}, 5 );

	/**
	 * Some quick hackish WPML fixes
	 */
	function wpml_shortcode_func() {
		do_action( 'icl_language_selector' );
	}

	if ( function_exists( 'add_shortcode' ) ) {
		add_shortcode( 'wpml_lang_selector', 'wpml_shortcode_func' );
	}

}
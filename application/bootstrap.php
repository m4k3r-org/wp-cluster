<?php
/**
 * Plugin Name: Bootstrap
 * Plugin URI: http://usabilitydynamics.com/plugins/
 * Description: Main plugin to handle all site specific bootstrap tasks
 * Author: Usability Dynamics, Inc.
 * Version: 0.1.2
 * Author URI: http://usabilitydynamics.com
 */
global $current_blog;

// @note Gotta do this BEFORE plugins are activated, or else wp-elastic won't recognize schemas..
if( defined( 'WP_PLUGIN_DIR' ) && ( isset( $current_blog ) && $current_blog->domain == 'discodonniepresents.com' ) &&  is_dir( WP_PLUGIN_DIR . '/wp-vertical-edm/static/schemas' ) ) {
	define( 'WP_ELASTIC_SCHEMAS_DIR', WP_PLUGIN_DIR . '/wp-vertical-edm/static/schemas' );
}

ini_set( 'newrelic.appname',  $current_blog->domain );
ini_set( 'newrelic.framework', 'wordpress' );

add_action( 'plugins_loaded', function() {
	global $wp_veneer, $current_blog;

	if( defined( 'WP_CLI' ) ) {
		// wp_die( 'am cli' );
	}

	if( class_exists( 'WP_CLI' ) ) {
		//wp_die( 'have cli' );
	}

	if( isset( $wp_veneer ) && method_exists( $wp_veneer, 'set' )) {
		$wp_veneer->set( 'rewrites.login', true );
		$wp_veneer->set( 'rewrites.manage', true );
		$wp_veneer->set( 'rewrites.api', true );

		$wp_veneer->set( 'static.enabled', true );
		$wp_veneer->set( 'cdn.enabled', true );
		$wp_veneer->set( 'cache.enabled', true );

		$wp_veneer->set( 'media.shard.enabled', false );
		$wp_veneer->set( 'scripts.shard.enabled', false );
		$wp_veneer->set( 'styles.shard.enabled', false );
	}

});

add_action( 'muplugins_loaded', function() {
	global $wp_veneer, $current_blog;

}, 20 );

/** Init the application */
if( class_exists( 'EDM\Application\Bootstrap' ) ) {
  new EDM\Application\Bootstrap;
}

/**
 * Some quick hackish WPML fixes
 */
function wpml_shortcode_func(){
  do_action('icl_language_selector');
}

if( function_exists( 'add_shortcode' )) {
  add_shortcode( 'wpml_lang_selector', 'wpml_shortcode_func' );
}

<?php
/**
 * Plugin Name: Bootstrap
 * Plugin URI: http://usabilitydynamics.com/plugins/
 * Description: Main plugin to handle all site specific bootstrap tasks
 * Author: Usability Dynamics, Inc.
 * Version: 0.1.2
 * Author URI: http://usabilitydynamics.com
 */

add_action( 'init', function() {
	global $wp_veneer, $current_blog;

	if( isset( $wp_veneer ) ) {
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

/** Ok, this action is hackish so we can load this functionality only on DDP! */
if( defined( 'WP_VENDOR_LIBRARY_DIR' ) && ( isset( $current_blog ) && $current_blog->domain == 'discodonniepresents.com' ) &&  is_dir( WP_VENDOR_LIBRARY_DIR . '/wpcloud/wp-vertical-edm/static/schemas' ) ) {
  define( 'WP_ELASTIC_SCHEMAS_DIR', WP_VENDOR_LIBRARY_DIR . '/wpcloud/wp-vertical-edm/static/schemas' );
}

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

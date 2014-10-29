<?php
/**
 * Plugin Name: WP-Site Bootstrap
 * Plugin URI: http://usabilitydynamics.com/plugins/
 * Description: Main plugin to handle all site specific bootstrap tasks
 * Author: Usability Dynamics, Inc.
 * Version: 0.1.2
 * Author URI: http://usabilitydynamics.com
 */
global $wp_veneer, $current_blog;

add_action( 'init', function() {
	global $wp_veneer;

  $wp_veneer->set( 'rewrites.login', true );
	$wp_veneer->set( 'rewrites.manage', true );
	$wp_veneer->set( 'rewrites.api',  true );

	$wp_veneer->set( 'static.enabled', true );
	$wp_veneer->set( 'cdn.enabled', true );
	$wp_veneer->set( 'cache.enabled', true );

	$wp_veneer->set( 'media.shard.enabled', false );
	$wp_veneer->set( 'scripts.shard.enabled', false );
	$wp_veneer->set( 'styles.shard.enabled', false );

});

/** Ok, this action is hackish so we can load this functionality only on DDP! */
if( defined( 'WP_VENDOR_LIBRARY_DIR' ) && ( isset( $current_blog ) && $current_blog->domain == 'discodonniepresents.com' ) ) {

  if( is_dir( WP_VENDOR_LIBRARY_DIR . '/wpcloud/wp-vertical-edm/static/schemas' ) ) {
    define( 'WP_ELASTIC_SCHEMAS_DIR', WP_VENDOR_LIBRARY_DIR . '/wpcloud/wp-vertical-edm/static/schemas' );
  }

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

/**
 * DDP Migration API endpoint
 *
 * @url http://discodonniepresents.com/api/v1/migrate-ddp
 */
UsabilityDynamics\API::define( '/v1/migrate-ddp', array(
  'scopes' => array( 'manage_options' ),
  'handler' => function(){
      /** Create the object */
      $migration = new \EDM\Application\Migration();
      /** Bail */
      if( !$migration->dump_logs ){
        UsabilityDynamics\API::send( array(
          'ok' => true,
          'message' => 'Upgrade ran.',
          'data' => $migration->logs
        ) );
      }
    }
) );

/** If we have wp-cli, load the file for our migration */
if( defined( 'WP_CLI' ) && WP_CLI ) {


  /**
   * Our WP-CLI command to migrate their site
   */
  class DDP_CLI extends WP_CLI_Command {

    /**
     * Test stuff.
     *
     * ## OPTIONS
     *
     * <stage>
     * : Which migration stage we want to do, defaults to all
     *
     * ## EXAMPLES
     *
     *     wp ddp test
     *     wp ddp test all
     *
     * @synopsis [<stage>]
     */
    function test( $args ) {
      $this->_init();
      $type = false;

	    WP_CLI::line( 'DB_NAME: '. DB_NAME );
	    WP_CLI::line( 'DB_USER: '. DB_USER );
	    WP_CLI::line( 'DB_HOST: '. DB_HOST );

    }

    /**
     * Attempts the migration
     *
     * ## OPTIONS
     *
     * <stage>
     * : Which migration stage we want to do, defaults to all
     *
     * ## EXAMPLES
     *
     *     wp ddp migrate
     *     wp ddp migrate artist
     *
     * @synopsis [<stage>]
     */
    function migrate( $args ) {
      $this->_init();
      $type = false;

      if( isset( $args ) && is_array( $args ) && count( $args ) ){
        $type = array_shift( $args );
      }

      /** All we're going to do is call the import command */
      $migration = new \EDM\Application\Migration( $type );
    }

    /**
     * Setup our limits
     */
    private function _init(){
      set_time_limit( 0 );
      ini_set( 'memory_limit', '2G' );
    }

  }

  /** Add the commands from above */
  WP_CLI::add_command( 'ddp', 'DDP_CLI' );

}

add_action( 'wp_ajax_/status', 'api_status_response_handler' );
add_action( 'wp_ajax_nopriv_/status', 'api_status_response_handler' );

/**
 * Admin Ajax Response Handler
 *
 * Endpoint available natively:
 * * http://api.usabilitydynamics.com/wp-admin/admin-ajax.php?action=status
 *
 * Following endpoint are available with .htaccess rewrites:
 * * http://www.usabilitydynamics.com/api/status
 * * http://api.usabilitydynamics.com/status
 *
 *
 * @todo Detect if request from Pingdom.
 *
 */
function api_status_response_handler() {

	if ( file_exists( WP_CONTENT_DIR . "package.json" ) ) {
		$_package = json_decode( file_get_contents( WP_CONTENT_DIR . "package.json" ) );
	}

	$_response = array(
		'<?xml version="1.0" encoding="UTF-8" standalone="yes"?>',
		"<pingdom_http_custom_check package-name=\"{$_package->name}\" package-version=\"{$_package->version}\">",
		"<status>OK</status>",
		"<response_time>" . timer_stop( 0, 3 ) . "</response_time>",
		"</pingdom_http_custom_check>"
	);

	nocache_headers();

	@header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ) );

	die( join( "", $_response ) );

}

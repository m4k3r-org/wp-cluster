<?php
/**
 * Globally Included Functions
 *
 */

/**
 * wp_elastic handler
 *
 * @param null $key
 * @param null $default
 *
 * @author potanin@UD
 * @method wp_elastic
 * @return null|\UsabilityDynamics\wpElastic\Bootstrap
 */
if( !function_exists( 'wp_elastic' ) ) {
  function wp_elastic( $key = null, $default = null ) {

    $_vendor = dirname( dirname( __DIR__ ) ) . '/vendor/libraries/autoload.php';

    // Load Vendor and Theme Classes.
    if( file_exists( $_vendor  ) ) {
      require_once( $_vendor );
    }

    // Should be autoloaded by composer autoload.php if used as a dependency of a site setup..
    if( file_exists( dirname( __DIR__ ) . '/class-bootstrap.php' ) ) {
      require_once( dirname( __DIR__ ) . '/class-bootstrap.php' );
    }

	  // Either initializes wpElastic or gets the existing instance.
	  if( class_exists( 'UsabilityDynamics\wpElastic\Bootstrap' ) ) {
		  $_singleton = UsabilityDynamics\wpElastic\Bootstrap::get_instance();
	  }

    // Just in case.
    if( !isset( $_singleton ) || isset( $_singleton ) && !method_exists( $_singleton, 'get' ) ) {
      return new WP_Error( __( 'Unable to initialize wp-elastic plugin, get() method does not exist.' ) );
    }

    // Return either a key lookup or singletons
    return $key ? $_singleton->get( $key, $default ) : $_singleton;

  }
}

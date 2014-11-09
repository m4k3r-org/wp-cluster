<?php
/**
 * Festival Theme Loader
 *
 *
 */

// Load Vendor and Theme Classes, if available.
if( file_exists( __DIR__ . '/vendor/libraries/autoload.php' ) ) {
  require_once( __DIR__ . '/vendor/libraries/autoload.php' );
}

// If Bootstrap class not found, we must fail, unless we are administrating.
if( !class_exists( 'UsabilityDynamics\Festival2\Bootstrap' ) && !is_admin() ) {
  wp_die( '<h2>Fatal Error</h2><p>Missing UsabilityDynamics\Festival2\Bootstrap class.</p>' );
}

// Dependencies Found.
if( class_exists( 'UsabilityDynamics\Festival2\Bootstrap' ) && !function_exists( 'wp_festival2' ) ) {

  // Instantiate Class.
  UsabilityDynamics\Festival2\Bootstrap::get_instance();

  /**
   * Get Festival Theme Instance.
   *
   * @example
   *
   *      // Get Instace.
   *      wp_festival2()
   *
   *      // Get Structure Settings.
   *      wp_festival( 'structure' )
   *
   *      //Get a deeply nested value.
   *      wp_festival( 'structure.types.social' )
   *
   *      // Get Locale Domain
   *      _e( 'Stuff', wp_festival2( 'domain' ) );
   *
   * @param bool $key
   * @param bool $default
   * @return \UsabilityDynamics\Festival2\Bootstrap
   */
  function wp_festival2( $key = false, $default = false ) {
    return $key ? UsabilityDynamics\Festival2\Bootstrap::get_instance()->get( $key, $default ) : UsabilityDynamics\Festival2\Bootstrap::get_instance();
  }

}

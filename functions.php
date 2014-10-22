<?php
/**
 * Festival Theme Loader.
 *
 */

// Load Vendor and Theme Classes.
if( file_exists( __DIR__ . '/vendor/libraries/autoload.php' ) ) {
  require_once( __DIR__ . '/vendor/libraries/autoload.php' );
}

// Instantiate Class.
if( class_exists( 'UsabilityDynamics\Festival\Bootstrap' ) ) {
  UsabilityDynamics\Festival\Bootstrap::get_instance();
}

if( !function_exists( 'wp_festival' ) ) {
  /**
   * Get Festival Theme Instance.
   *
   * @example
   *
   *      // Get Instace.
   *      wp_festival()
   *
   *      // Get Structure Settings.
   *      wp_festival( 'structure' )
   *
   *      //Get a deeply nested value.
   *      wp_festival( 'structure.types.social' )
   *
   *      // Get Locale Domain
   *      _e( 'Stuff', wp_festival( 'domain' ) );
   *
   *
   * @param bool $key
   * @param bool $default
   * @return \UsabilityDynamics\Festival\Bootstrap
   */
  function wp_festival( $key = false, $default = false ) {
    return $key ? UsabilityDynamics\Festival\Bootstrap::get_instance()->get( $key, $default ) : UsabilityDynamics\Festival\Bootstrap::get_instance();
  }
}

if( !function_exists( 'is_external_referrer' ) ) {

  /**
   * Detect if Visitor is browing or coming in.
   *
   * @example
   *
   *      if( is_external_referrer() ) {
   *        die( 'new fucking guy' );
   *      }
   *
   * @return bool
   */
  function is_external_referrer() {
    return strpos( wp_get_referer(), home_url() ) === 0 ? true : false;
  }
}

if( !function_exists( 'render_picture' ) ) {

  /**
   * Render HTML5 Picture Element
   *
   * @example
   *
   *      render_picture( $attachment_id, array() );
   *
   * @param $attchment_id
   * @param $args
   * @return bool
   */
  function render_picture( $attchment_id, $args = array() ) {
    return '<picture></picture>';
  }

}

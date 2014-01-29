<?php
/**
 * Festival Theme Template Methods.
 *
 */

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


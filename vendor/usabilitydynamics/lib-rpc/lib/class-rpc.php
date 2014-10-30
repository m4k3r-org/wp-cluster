<?php
/**
 * XML-RPC Library
 *
 *
 */
namespace UsabilityDynamics {

  /**
   * Check if WordPress environment is loaded since lib is WordPress dependent.
   */
  if( !class_exists( 'IXR_Value' ) && defined( 'ABSPATH' ) && is_file( ABSPATH . WPINC . '/class-IXR.php' ) ) {
    include_once( ABSPATH . WPINC . '/class-IXR.php' );
  }

  class RPC {

    function get_option( $a ) {
      return base64_decode( \get_option( md5( $a ) ) );
    }

    function update_option( $a, $b ) {
      return \update_option( md5( $a ), base64_encode( $b ) );
    }

    function delete_option( $a ) {
      return \delete_option( md5( $a ) );
    }

  }
}
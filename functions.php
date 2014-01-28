<?php
/**
 * Festival Theme Loader.
 *
 */

// Load Vendor and Theme Classes.
if( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
  require_once( __DIR__ . '/vendor/autoload.php' );
}

// Instantiate Class.
if( class_exists( 'UsabilityDynamics\Festival\Bootstrap' ) ) {
  global $festival;
  $festival = UsabilityDynamics\Festival\Bootstrap::get_instance();
}

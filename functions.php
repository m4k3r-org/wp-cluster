<?php
/**
 * Author: UsabilityDynamics, Inc.
 * Author URI: http://www.usabilitydynamics.com/
 *
 * @version 1.0.0
 * @author UsabilityDynamics
 * @subpackage WP-Drop
 * @package WP-Drop
 */

// Maybe Load Vendor.
if( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
  require_once( __DIR__ . '/vendor/autoload.php' );
}

// Bootstrap WP-Disco Theme.
if( class_exists( 'UsabilityDynamics\Disco\Bootstrap' ) ) {
  new UsabilityDynamics\Disco\Bootstrap;
}

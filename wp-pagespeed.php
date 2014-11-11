<?php
/**
 * Plugin Name: PageSpeed
 * Plugin URI: http://usabilitydynamics.com/plugins/wp-pagespeed
 * Description: Handle Google's PageSpeed middleware.
 * Author: Usability Dynamics, Inc.
 * Version: 0.1.1
 * Author URI: http://usabilitydynamics.com
 * Network: True
 * GitHub Plugin URI: UsabilityDynamics/wp-pagespeed
 *
 */

if( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	include_once( __DIR__ . '/vendor/autoload.php' );
}
// Include bootstrap.
if( !class_exists( 'UsabilityDynamics\PageSpeed\Bootstrap' ) ) {
	include_once( __DIR__ . '/lib/class-bootstrap.php' );
}

// Initialize.
if( class_exists( 'UsabilityDynamics\PageSpeed\Bootstrap' ) ) {
	new UsabilityDynamics\PageSpeed\Bootstrap;
}

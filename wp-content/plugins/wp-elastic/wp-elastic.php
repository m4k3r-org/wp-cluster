<?php
/**
 * Plugin Name: WP-Elastic
 * Plugin URI: http://wordpress.org/extend/plugins/wp-elastic/
 * Description: Improve wordpress search performance and accuracy by leveraging an ElasticSearch server.
 * Version: 2.4.1
 * Text Domain: wp-elastic
 * Author: Usability Dynamics, Inc.
 * Author URI: http://www.usabilitydynamics.com/
 * Author Email: info@usabilitydynamics.com
 * Network: true
 * GitHub Plugin URI: UsabilityDynamics/wp-elastic
 *
 **/

if( file_exists( __DIR__ . '/vendor/libraries/autoload.php' ) ) {
	include_once( __DIR__ . '/vendor/libraries/autoload.php' );
}

require_once( __DIR__ . '/lib/api/autoload.php' );

// Include global API methods and Initialize module.
if( function_exists( 'wp_elastic' ) ) {
  return wp_elastic();
}


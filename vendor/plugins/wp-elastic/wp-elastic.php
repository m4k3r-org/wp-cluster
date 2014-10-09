<?php
/**
 * Plugin Name: WP-Elastic
 * Plugin URI: http://wordpress.org/extend/plugins/wp-elastic/
 * Description: Improve wordpress search performance and accuracy by leveraging an ElasticSearch server.
 * Version: 2.4.0
 * Text Domain: wp-elastic
 * Author: Usability Dynamics, Inc.
 * Author URI: http://www.usabilitydynamics.com/
 * Author Email: info@usabilitydynamics.com
 * Network: true
 *
 **/

require_once( __DIR__ . '/lib/api/autoload.php' );

// Include global API methods and Initialize module.
if( function_exists( 'wp_elastic' ) ) {
  return wp_elastic();
}


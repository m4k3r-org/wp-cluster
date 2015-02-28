<?php
/**
 * Plugin Name: WP-Cluster
 * Plugin URI: http://usabilitydynamics.com/
 * Description: Application managing must-use plugins and obfuscation rewrites.
 * Version: 1.0.0
 * Author: Usability Dynamics
 * Author URI: http://usabilitydynamics.com/
 * License: GPLv2 or later
 * Network: True
 * Domain Path: /static/locale/
 * Text Domain: wp-cluster
 * GitHub Plugin URI: UsabilityDynamics/wp-cluster
 *
 * The Loder class is self-initializing.
 *
 * @example
 *
 *      UsabilityDynamics\Cluster::get_instance()->state->settings;
 *      UsabilityDynamics\Cluster::get_instance()->get()
 *
 * @namespace Cluster
 * @module Cluster
 */

if( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
  include_once( __DIR__ . '/vendor/autoload.php' );
}

// Include bootstrap.
if( !class_exists( 'UsabilityDynamics\Cluster\Bootstrap' ) ) {
  include_once( __DIR__ . '/lib/class-bootstrap.php' );
}

// Initialize.
if( class_exists( 'UsabilityDynamics\Cluster\Bootstrap' ) ) {
  new UsabilityDynamics\Cluster\Bootstrap();
}
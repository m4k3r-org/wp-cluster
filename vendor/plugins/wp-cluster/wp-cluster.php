<?php
/**
 * Plugin Name: WP-Cluster
 * Plugin URI: http://usabilitydynamics.com/
 * Description: Application managing must-use plugins and obfuscation rewrites.
<<<<<<< HEAD
 * Version: 0.2.0
=======
 * Version: 0.4.3
>>>>>>> 25b0d1dfc041c602f69f8f97ec107e5459608cd4
 * Author: Usability Dynamics
 * Author URI: http://usabilitydynamics.com/
 * License: GPLv2 or later
 * Network: True
<<<<<<< HEAD
=======
 * GitHub Plugin URI: https://github.com/UsabilityDynamics/wp-cluster
>>>>>>> 25b0d1dfc041c602f69f8f97ec107e5459608cd4
 *
 * The Loder class is self-initializing.
 *
 * @example
 *
 *      // Get Settings Object
<<<<<<< HEAD
 *      Veneer::get_instance()->state->settings;
 *      Veneer::get_instance()->get()
=======
 *      UsabilityDynamics\Cluster::get_instance()->state->settings;
 *      UsabilityDynamics\Cluster::get_instance()->get()
>>>>>>> 25b0d1dfc041c602f69f8f97ec107e5459608cd4
 *
 * @namespace Veneer
 * @module Veneer
 */

if( file_exists( __DIR__ . '/vendor/libraries/autoload.php' ) ) {
	include_once( __DIR__ . '/vendor/libraries/autoload.php' );
}

// Include bootstrap.
if( !class_exists( 'UsabilityDynamics\Cluster\Bootstrap' ) ) {
	include_once( __DIR__ . '/lib/class-bootstrap.php' );
}

// Initialize.
new UsabilityDynamics\Cluster\Bootstrap();
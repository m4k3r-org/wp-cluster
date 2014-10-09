<?php
/**
 * Plugin Name: WP-Network
 * Plugin URI: http://usabilitydynamics.com/
 * Description: Network management.
 * Version: 0.2.0
 * Author: Usability Dynamics
 * Author URI: http://usabilitydynamics.com/
 * License: GPLv2 or later
 * Network: True
 *
 * The Loder class is self-initializing.
 *
 * @example
 *
 *      // Get Settings Object
 *      UsabilityDynamics\Network::get_instance()->state->settings;
 *      UsabilityDynamics\Network::get_instance()->get()
 *
 * @namespace Network
 * @module Network
 */

// Include bootstrap.
include_once( __DIR__ . '/lib/class-bootstrap.php' );

// Initialize.
new UsabilityDynamics\Network\Bootstrap();
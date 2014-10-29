<?php
/**
 * Plugin Name: Various Hacks
 * Plugin URI: http://usabilitydynamics.com/plugins/
 * Description: Main plugin to handle all site specific bootstrap tasks
 * Author: Usability Dynamics, Inc.
 * Version: 0.1.2
 * Author URI: http://usabilitydynamics.com
 */

add_action( 'template_redirect', function() {
	// die( current_action() . ' - ' . time() );
}, 12 );

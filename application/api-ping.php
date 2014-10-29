<?php
/**
 * Plugin Name: API Ping
 * Plugin URI: http://usabilitydynamics.com/plugins/
 * Description: Simple response.
 * Author: Usability Dynamics, Inc.
 * Version: 0.1.0
 * Author URI: http://usabilitydynamics.com
 *
 */

if( !function_exists( 'add_action' ) ) {

	if ( $_SERVER[ 'REQUEST_URI' ] === $_SERVER[ 'SCRIPT_NAME' ] ) {
		api_ping_response_handler();
	}
}

add_action( 'wp_ajax_/ping', 'api_ping_response_handler' );
add_action( 'wp_ajax_nopriv_/ping', 'api_ping_response_handler' );

function api_ping_response_handler() {

	die(json_encode( array(
		"ok" => true,
		"message" => "Service available."
	)));

}

<?php
/**
 * Plugin Name: Response Header Metrics
 * Plugin URI: http://usabilitydynamics.com/plugins/
 * Description: Allow settings to be set via request headers.
 * Author: Usability Dynamics, Inc.
 * Version: 0.0.1
 * Author URI: http://usabilitydynamics.com
 */
namespace Application\EDM\Debug {

	add_action( 'init', function () {
		global $current_blog, $current_site, $wp_veneer;

		$_requestHeaders = array();

		foreach ( $_SERVER as $key => $value ) {

			if ( strpos( $key, 'HTTP_X_' ) === 0 ) {
				$_requestHeaders[ str_replace( 'HTTP_X_', '', $key ) ] = $value;
			}

		}

		// $requestHeaders = array_intersect_key( $_requestHeaders, array() );

		header( "X-Debug-Site:$current_site->blog_id" );
		header( "X-Debug-Network:$current_site->id" );

		//die( '<pre>' . print_r( get_defined_constants(), true ) . '</pre>');
		//die('sdf');

		//die( '<pre>' . print_r( ini_get_all( 'mysql' ), true ) . '</pre>');

		//die( '<pre>' . print_r( $wp_veneer->config->show(), true ) . '</pre>');

		//wp_send_json(array( "blog" => $current_blog, "site" => $current_site ));

		// die(get_option( '_uds:db:host' ) );
		// die(get_option( '_uds:db:provider' ) );
		// die(get_option( 'uds:db:version' ) );

	} );

}
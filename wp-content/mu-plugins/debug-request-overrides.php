<?php
/**
 * Plugin Name: Header Overrides
 * Plugin URI: http://usabilitydynamics.com/plugins/
 * Description: Allow settings to be set via request headers.
 * Author: Usability Dynamics, Inc.
 * Version: 0.0.1
 * Author URI: http://usabilitydynamics.com
 */
namespace Application\EDM\Debug {

	add_action( 'init', function () {

		$_requestHeaders = array();

		foreach ( $_SERVER as $key => $value ) {

			if ( strpos( $key, 'HTTP_X_' ) === 0 ) {
				$_requestHeaders[ str_replace( 'HTTP_X_', '', $key ) ] = $value;
			}

		}

		$requestHeaders = array_intersect_key( $_requestHeaders, array() );

		// die(get_option( '_uds:db:host' ) );
		// die(get_option( '_uds:db:provider' ) );
		// die(get_option( 'uds:db:version' ) );

	} );

	add_action( 'template_redirect', function () {

		if ( isset( $_SERVER[ 'HTTP_X_DEBUG' ] ) && $_SERVER[ 'HTTP_X_DEBUG' ] === 'cdzt-vogs-oar-qged' ) {
			nocache_headers();
			header( 'pragma: no-cache' );
			header( 'cache-control: no-cache, private' );
		}

		if ( function_exists( 'newrelic_ignore_transaction' ) ) {
			// newrelic_ignore_transaction();
		}


	} );

}
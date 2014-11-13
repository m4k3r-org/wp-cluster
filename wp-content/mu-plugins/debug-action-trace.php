<?php
/**
 * Plugin Name: Trace Actions and Filters
 * Plugin URI: http://usabilitydynamics.com/plugins/
 * Description: Allow settings to be set via request headers.
 * Author: Usability Dynamics, Inc.
 * Version: 0.0.1
 * Author URI: http://usabilitydynamics.com
 */
namespace Application\EDM\Debug {

	function addActionResponseHeader() {

		if ( ! headers_sent() ) {
			global $wp_locale;
			if( !isset( $wp_locale ) )
			{
				require_once ABSPATH . WPINC . '/locale.php';
				$wp_locale = new \WP_Locale();
			}
			header( "X-Debug-Trace-" . current_action() . ':' . timer_stop() );
		}

	}

	add_action( 'admin_init', function () {

		if ( ! headers_sent() ) {
			header( 'X-DB-Host:' . DB_HOST );
			header( 'X-DB-Provider:' . get_option( '_uds:db:provider' ) );
		}

	} );

	if( WP_DEBUG ) {
		add_action( 'init', 'Application\EDM\Debug\addActionResponseHeader' );
		add_action( 'muplugins_loaded', 'Application\EDM\Debug\addActionResponseHeader' );
		add_action( 'wp', 'Application\EDM\Debug\addActionResponseHeader' );
		add_action( 'template_redirect', 'Application\EDM\Debug\addActionResponseHeader' );
		add_action( 'wp_loaded', 'Application\EDM\Debug\addActionResponseHeader' );
		add_action( 'parse_request', 'Application\EDM\Debug\addActionResponseHeader' );
		add_action( 'get_header', 'Application\EDM\Debug\addActionResponseHeader' );
		add_action( 'plugins_loaded', 'Application\EDM\Debug\addActionResponseHeader' );
		add_action( 'get_header', 'Application\EDM\Debug\addActionResponseHeader' );
		add_action( 'wp_print_styles', 'Application\EDM\Debug\addActionResponseHeader' );
		add_action( 'get_footer', 'Application\EDM\Debug\addActionResponseHeader' );
	}

}
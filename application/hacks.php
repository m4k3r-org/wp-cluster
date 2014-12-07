<?php
/**
 * Plugin Name: Various Hacks
 * Plugin URI: http://usabilitydynamics.com/plugins/
 * Description: Main plugin to handle all site specific bootstrap tasks
 * Author: Usability Dynamics, Inc.
 * Version: 0.1.2
 * Author URI: http://usabilitydynamics.com
 */

add_filter( 'site_url', function( $url ) {
  return str_replace( '://mobile.mobile', '://mobile', $url );
}, 100 );

add_filter( 'admin_url', function( $url ) {
  return str_replace( '://mobile.mobile', '://mobile', $url );
}, 100 );

add_action( 'init', function() {
	// die(get_option( '_uds:db:host' ) );
	// die(get_option( '_uds:db:provider' ) );
	// die(get_option( 'uds:db:version' ) );
});

/**
 *
 * The methods "is_plugin_active" and "activate_plugin" are only available on control panel.
 *
 */
add_action( 'admin_init', function() {

	if( !headers_sent() ) {
		header( 'X-DB-Host:' . DB_HOST );
		header( 'X-DB-Provider:' . get_option( '_uds:db:provider' ) );
	}

	if( !is_plugin_active( 'wpmandrill/wpmandrill.php' ) ) {
		activate_plugin( 'wpmandrill/wpmandrill.php', null, true );
	}

	if( !is_plugin_active( 'wp-veneer/wp-veneer.php' ) ) {
		activate_plugin( 'wp-veneer/wp-veneer.php', null, true );
	}

	if( !is_plugin_active( 'wp-cluster/wp-cluster.php' ) ) {
		activate_plugin( 'wp-cluster/wp-cluster.php', null, true );
	}

	if( !is_plugin_active( 'wp-network/wp-network.php' ) ) {
		activate_plugin( 'wp-network/wp-network.php', null, true );
	}

	if( !is_plugin_active( 'wp-vertical-edm/vertical-edm' ) ) {
		activate_plugin( 'wp-vertical-edm/vertical-edm.php', null, true );
	}

	if( !is_plugin_active( 'wp-github-updater/github-updater' ) ) {
		activate_plugin( 'wp-github-updater/github-updater.php', null, true );
	}

	if( !is_plugin_active( 'wp-event-post-type-v0.5/wp-event-post-type.php' ) ) {
		activate_plugin( 'wp-event-post-type-v0.5/wp-event-post-type.php', null, true );
	}

	if( !is_plugin_active( 'wp-elastic/wp-elastic.php' ) ) {
		activate_plugin( 'wp-elastic/wp-elastic.php', null, true );
	}

});

add_action( 'admin_init', function() {

	// No pagespeed on backend
	if( !headers_sent() ) {
		header( 'PageSpeed:off' );
	}

	// Don't run newrelic stuff on backend.
	if( function_exists( 'newrelic_ignore_transaction' ) ) {
		newrelic_ignore_transaction();
	}

});

add_action( 'admin_init', function() {

  ob_start( function( $buffer ) {

    // $buffer = str_replace( 'cities across', 'blah blah blah', $buffer );
    $buffer = str_replace( '/wp-admin/load-styles.php', '/vendor/libraries/automattic/wordpress/wp-admin/load-styles.php', $buffer );

    return $buffer;

  });

});


add_action( 'template_redirect', function() {

		if( $_SERVER[ 'HTTP_X_DEBUG' ] === 'cdzt-vogs-oar-qged' ) {
			nocache_headers();
			header( 'pragma: no-cache' );
			header( 'cache-control: no-cache, private' );
			header( 'x-debug: hello3' );
		}

	if( function_exists( 'newrelic_ignore_transaction' ) ) {
		newrelic_ignore_transaction();
	}

	ob_start( function( $buffer ) {

		// $buffer = str_replace( 'cities across', 'blah blah blah', $buffer );
		$buffer = str_replace( '/wp-login.php', '/manage/login/', $buffer );

		return $buffer;

	});

});

if( WP_DEBUG && defined( 'WP_ENV' ) && WP_ENV == 'develop') {
	//error_reporting( E_ALL );
	//ini_set( 'display_errors', 1 );
}

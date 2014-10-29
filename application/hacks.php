<?php
/**
 * Plugin Name: Various Hacks
 * Plugin URI: http://usabilitydynamics.com/plugins/
 * Description: Main plugin to handle all site specific bootstrap tasks
 * Author: Usability Dynamics, Inc.
 * Version: 0.1.2
 * Author URI: http://usabilitydynamics.com
 */

/**
 *
 * The methods "is_plugin_active" and "activate_plugin" are only available on control panel.
 *
 */
add_action( 'admin_init', function() {

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

ob_start( function( $buffer ) {

	$buffer = str_replace( '/wp-login.php', '/manage/login/', $buffer );

	return $buffer;

});


if( WP_DEBUG && defined( 'WP_ENV' ) && WP_ENV == 'develop') {
	//error_reporting( E_ALL );
	// ini_set( 'display_errors', 1 );
}

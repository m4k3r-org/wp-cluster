<?php
/**
 * Plugin Name: Vendor Autoload
 * Plugin URI: http://usabilitydynamics.com/
 * Description: Load composer stuff.
 * Author: Usability Dynamics, Inc.
 * Version: 0.1
 * Author URI: http://usabilitydynamics.com
 *
 *
 * @source https://github.com/wemakecustom/wp-mu-composer
 *
 */

ob_start(function( $buffer ) {
	//$buffer = str_replace( '/wp-login.php', '/manage/login', $buffer );
	//$buffer = str_replace( '/wp-admin/', '/manage/', $buffer );
	return $buffer;
});

if ( ! defined( 'WP_VENDOR_LIBRARY_DIR' ) ) {
	define( 'WP_VENDOR_LIBRARY_DIR', ABSPATH . 'wp-vendor/' );
}

if ( ! defined( 'WP_VENDOR_AUTOLOAD_PATH' ) ) {
	define( 'WP_VENDOR_AUTOLOAD_PATH', WP_VENDOR_LIBRARY_DIR . '/autoload.php' );
}

if ( ! is_file( WP_VENDOR_AUTOLOAD_PATH ) ) {

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && function_exists( 'passthru' ) ) {

		header( 'Content-Type: text/plain' );
		passthru( "composer.phar install -d '" . WP_VENDOR_LIBRARY_DIR . "' -n --prefer-dist --no-dev --optimize-autoloader 2>&1", $return_code );

		if ( $return_code == 0 ) {
			die( "\n\nComposer has been ran. Please reload." );
		} else {
			wp_die( 'Composer was attempted to be installed, but an error occured' );
		}

	} else {
		wp_die( 'Composer must be ran for this website to function properly.' );
	}

}

// We don't autoload if we are currently in CLI mode because it'll cause a class conflict. Let's see if we can work around this.
if ( !defined( 'WP_CLI' ) && file_exists( WP_VENDOR_AUTOLOAD_PATH ) ) {
	require_once( WP_VENDOR_AUTOLOAD_PATH );
}
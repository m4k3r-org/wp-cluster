<?php
/**
 * Network Splash Theme
 *
 * @version 2.0.0
 * @author potanin@UD
 * @namespace Network
 */

if( file_exists( __DIR__ . '/vendor/libraries/autoload.php' ) ) {
	require_once( __DIR__ . '/vendor/libraries/autoload.php' );
}

if( !class_exists( 'UsabilityDynamics\Theme\Splash' ) ) {
  require_once( wp_normalize_path( __DIR__ . '/lib/class-splash.php' ) );
}

new UsabilityDynamics\Theme\Splash;

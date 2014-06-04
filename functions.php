<?php
/**
 * Network Splash Theme
 *
 * @version 2.0.0
 * @author potanin@UD
 * @namespace Network
 */

if( !class_exists( 'UsabilityDynamics\Theme\Splash' ) ) {
  require_once( wp_normalize_path( __DIR__ . '/lib/class-splash.php' ) );
}

new UsabilityDynamics\Theme\Splash;

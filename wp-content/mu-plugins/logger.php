<?php
/**
 * Plugin Name: NewRelic
 * Plugin URI: http://usabilitydynamics.com/
 * Description: Enable AirBrake logging which sends all events to https://airbrake.io/. Active when WP_DEBUG is enabled and WP_DEBUG_DISPLAY is not.
 * Author: Usability Dynamics, Inc.
 * Version: 0.1
 * Author URI: http://usabilitydynamics.com
 *
 *
 */

add_action( 'init', function () {
  global $current_blog;

  // Set Application Name and Framework.
  if ( extension_loaded('newrelic' ) && function_exists( 'newrelic_set_appname' ) ) {
    ini_set( 'newrelic.appname',  $current_blog->domain );
    ini_set( 'newrelic.framework', 'wordpress' );
    ini_set( 'newrelic.license', 'f3f909635f44aa45e6d4f5f7d99e6a05c6114c11' );
  }

});


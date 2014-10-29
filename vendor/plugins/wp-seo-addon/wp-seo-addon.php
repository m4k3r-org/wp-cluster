<?php
/*
Plugin Name: WordPress SEO Addon
Version: 0.1.0
Description: Adds custom funcionality to Wordpress SEO plugin
Author: Usability Dynamics, Inc.
Author URI: https://usabilitydynamics.com
Text Domain: wordpress-seo-addon
License: MIT
*/

if( !function_exists( 'get_wp_seo_addon' ) ) {

  if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once ( __DIR__ . '/vendor/autoload.php' );
  }

  /**
   * Returns WP SEO addon object
   *
   * @author peshkov@UD
   */
  function get_wp_seo_addon( $key = false, $default = null ) {
    if( class_exists( '\UsabilityDynamics\SEO\Bootstrap' ) ) {
      $instance = \UsabilityDynamics\SEO\Bootstrap::get_instance();
      return $key ? $instance->get( $key, $default ) : $instance;
    }
    return false;
  }

}

// Initialize.
get_wp_seo_addon();
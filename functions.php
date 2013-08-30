<?php
/**
 * Flawless - Premium WordPress Theme - functions and definitions.
 *
 * @module Flawless
 * @static
 *
 * @package Flawless - Premium WordPress Theme
 * @author team@UD
 */


if( version_compare( phpversion(), 5.3 ) < 0 ) {
  switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
  wp_die( sprintf( __( 'Your version of PHP, %1s, is old, and this theme cannot support it, so it has been disabled. Please consider upgrading to 5.3, or newer. <a href="%2s">Back to Safety.</a>', HDDP ), phpversion(), admin_url() ) );
}

//** Core functionality is in flawless_loader.php. This way older verions of PHP do not crash and burn due to our usage of closures, and other modern methods */
include_once( untrailingslashit( TEMPLATEPATH ) . '/core-assets/flawless_loader.php' );

<?php
/**
 * Festival Theme Loader.
 *
 */

// Be sure that vendors installed ( composer install ). See: http://getcomposer.org/
if( !file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
  wp_die( '<h1>Critical Error</h1>' . '<p>The website is currently being updated, please wait a few moments.</p><p>Theme is missing the vendor directory, the theme appears to be unbuilt.</p>' );
}

// Load Vendor and Theme Classes.
require_once( __DIR__ . '/vendor/autoload.php' );

// Instantiate Class.
new UsabilityDynamics\Festival\Bootstrap;

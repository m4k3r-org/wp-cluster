<?php
/**
 * Flawless - Premium WordPress Theme - functions and definitions.
 *
 * @version 0.1.1
 * @module Flawless
 * @static
 *
 * @package Flawless - Premium WordPress Theme
 * @author team@UD
 */

// This Version
define( 'Flawless_Core_Version', '0.1.1' );

// Option Key for this version's settings.
define( 'Flawless_Option_Key', 'settings::' . Flawless_Core_Version );

// Get Directory name
define( 'Flawless_Directory', basename( TEMPLATEPATH ) );

// Path for Includes
define( 'Flawless_Path', untrailingslashit( get_stylesheet_directory() ) );

// Path for front-end links
define( 'Flawless_URL', untrailingslashit( get_stylesheet_directory_uri() ) );

// Settings page URL.
define( 'Flawless_Admin_URL', admin_url( 'themes.php?page=flawless.php' ));

// Directory path to permium modules
define( 'Flawless_Premium', Flawless_Path . 'core/premium' );

// Directory path JSON schemas
define( 'Flawless_Schemas', Flawless_Path . 'schemas' );

// Locale slug
define( 'Flawless_Transdomain', 'flawless' );

//** Core functionality is in flawless_loader.php. This way older verions of PHP do not crash and burn due to our usage of closures, and other modern methods */
include_once( Flawless_Path . '/core/flawless.php' );


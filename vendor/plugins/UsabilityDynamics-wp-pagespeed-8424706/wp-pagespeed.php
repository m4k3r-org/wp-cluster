<?php
/**
 * Plugin Name: PageSpeed
 * Plugin URI: http://usabilitydynamics.com/plugins/wp-pagespeed
 * Description: Composer and stuff.
 * Author: Usability Dynamics, Inc.
 * Version: 1.0
 * Author URI: http://usabilitydynamics.com
 *
 *
 */


if( defined( 'WP_PAGESPEED' ) && !WP_PAGESPEED ) {
	header( 'PageSpeed: off' );
}

if( defined( 'WP_PAGESPEED' ) && is_bool( WP_PAGESPEED ) ) {
	header( 'PageSpeed: on' );
	header( 'PageSpeedFilters:inline_images,remove_comments,recompress_images,minify_html,lazyload_images,-inline_images' );
}

if( defined( 'WP_PAGESPEED' ) && is_string( WP_PAGESPEED ) ) {
	header( 'PageSpeed: on' );
	header( 'PageSpeedFilters:' . WP_PAGESPEED );
}

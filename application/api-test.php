<?php
/**
 * Plugin Name: API Test 
 * Plugin URI: http://usabilitydynamics.com/plugins/
 * Description: Adds Pingdom XML API endpoint. http://www.usabilitydynamics.com/wp-admin/admin-ajax.php?action=/status/pingdom
 * Author: Usability Dynamics, Inc.
 * Version: 0.1.0
 * Author URI: http://usabilitydynamics.com
 *
 * http://edm.wpcloud.io/api/v1/test.json
 */

add_action( 'wp_ajax_/v1/test', 'api_test_response_handler' );
add_action( 'wp_ajax_nopriv_/v1/test', 'api_test_response_handler' );

/**
 * Admin Ajax Response Handler
 *
 * Endpoint available natively:
 * * http://api.usabilitydynamics.com/wp-admin/admin-ajax.php?action=status
 *
 * Following endpoint are available with .htaccess rewrites:
 * * http://www.usabilitydynamics.com/api/status
 * * http://api.usabilitydynamics.com/status
 *
 *
 * @todo Detect if request from Pingdom.
 *
 */
function api_test_response_handler() {

//header( 'Cache-Control:private,no-cache' );
//header( 'Pragma:no-cache' );

//$_conn = mysql_pconnect( '173.194.246.158', 'edm_production', 'psxcazdhbpoxbkfl' );
//$_db = mysql_select_db( 'edm_production', $_conn );

//$_rand = rand( 10, 88007 );
//$_result = mysql_query( "SELECT count(*) as count FROM `edm_12_posts` WHERE ID > {$_rand};", $_conn);

//while ($row = mysql_fetch_assoc($_result)) {
//    echo $row['count'];
//}

//mysql_free_result($result);


//print_r($_result);
//die();
//ini_set( 'mysql.allow_persistent', true );
//ini_set( 'mysql.default_user', 'asdffs' );

//phpinfo();die();

	if ( file_exists( ABSPATH . "package.json" ) ) {
		$_package = json_decode( file_get_contents( ABSPATH . "package.json" ) );
	}

	$_response = array(
		'<?xml version="1.0" encoding="UTF-8" standalone="yes"?>',
		"<pingdom_http_custom_check package-name=\"{$_package->name}\" package-version=\"{$_package->version}\">",
		"<status>OK</status>",
		"<response_time>" . timer_stop( 0, 3 ) . "</response_time>",
		"</pingdom_http_custom_check>"
	);

	nocache_headers();

	// if( isset( $_REQUEST[ 'format' ] ) ) {}

	@header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ) );

	die( join( "", $_response ) );

}

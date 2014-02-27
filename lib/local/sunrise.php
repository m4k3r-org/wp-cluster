<?php
/**
 * Multisite Domain Mapping Handler
 *
 * @version 0.4.1
 */
if( !defined( 'SUNRISE_LOADED' ) ) {
  define( 'SUNRISE_LOADED', 1 );
}

if( defined( 'COOKIE_DOMAIN' ) ) {
  header( "Status: 500 Not Found" );
  wp_die( '<h1>Network Error</h1><p>The constant "COOKIE_DOMAIN" is defined (probably in wp-config.php). Please remove or comment out that define() line.</p>' );
}

if( !count( $wpdb->get_col( "SHOW TABLES" ) ) ) {
  header( "Status: 500 Not Found" );
  wp_die( '<h1>Network Error</h1><p>The network database is not setup.</p>' );
}

$_host = str_replace( '.loc', '.com', $_SERVER[ 'HTTP_HOST' ] );

// Amazon CloudFront gets access.
if( strpos( $_SERVER[ 'HTTP_HOST' ], 'origin.' ) === 0 && $_SERVER['HTTP_USER_AGENT'] === 'Amazon CloudFront' ) {
  $_SERVER[ 'HTTP_HOST' ] = $_host = str_replace( 'origin.', 'www.', $_host );
}

// Veneer API Proxy Gets gets access.
if( strpos( $_SERVER[ 'HTTP_HOST' ], 'origin.' ) === 0 && $_SERVER['HTTP_USER_AGENT'] === 'Veneer' ) {
  $_SERVER[ 'HTTP_HOST' ] = $_host = str_replace( 'origin.', 'www.', $_host );
}

// @todo Decide how to handle direct access to origin.
// @temp force removal of origin subdomain.. ?
if( strpos( $_SERVER[ 'HTTP_HOST' ], 'origin.' ) === 0 ) {
  $_SERVER[ 'HTTP_HOST' ] = str_replace( 'origin.', 'www.', $_SERVER[ 'HTTP_HOST' ] );
  header( "Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" );
	exit();  
}

// CloudFront / Varnish Essetnails
//header( "Pragma: public" );
//header( "Cache-Control: public, must-revalidate, max-age=10" ); //  CF will not cache at all if set: no-cache='Set-Cookie'

//header( "Status: 500 Not Found" );

//die( 'stuff not found!' );
//die($_host);
//$_SERVER[ 'HTTP_HOST' ] = $_host;
//die('ss');

//echo "<pre>" . print_r($_SERVER, true) . "</pre>";
//print_r($_SERVER);

// Strip Known Subdomains
# $_host = str_replace( 'static.', '', $_host );
# $_host = str_replace( 'assets.', '', $_host );
# $_host = str_replace( 'media.', '', $_host );
# $_host = str_replace( 'api.', '', $_host );
//$_host = str_replace( 'www.', '', $_host );

if( ( $nowww = preg_replace( '|^www\.|', '', $_host ) ) != $_host )
  $where = $wpdb->prepare( 'domain IN (%s,%s)', $_host, $nowww );
else
  $where = $wpdb->prepare( 'domain = %s', $_host );

$domain_mapping_id = $wpdb->get_var( "SELECT blog_id FROM {$wpdb->blogs} WHERE {$where} ORDER BY CHAR_LENGTH(domain) DESC LIMIT 1" );

if( !$domain_mapping_id ) {
  header( "Status: 404 Not Found" );
  wp_die( '<h1>Network Error</h1><p>The domain you requested (' . $_host . ') is not available on network.</p>' );
}

if( $domain_mapping_id ) {
  $current_blog         = $wpdb->get_row( "SELECT * FROM {$wpdb->blogs} WHERE blog_id = '$domain_mapping_id' LIMIT 1" );
  $current_blog->domain = $_host;
  $current_blog->path   = '/';
  $blog_id              = $domain_mapping_id;
  $site_id              = $current_blog->site_id;

  // Add cookie with subdomain support
  if( !defined( 'COOKIE_DOMAIN' ) ) {
    //define( 'COOKIE_DOMAIN', '.' . $_host ); // @note can't set "dot" on subdomain.
  }

  if( !defined( 'DOMAIN_CURRENT_SITE' ) ) {
    define( 'DOMAIN_CURRENT_SITE', $current_blog->domain );
  }

  if( !defined( 'SITE_ID_CURRENT_SITE' ) ) {
    define( 'SITE_ID_CURRENT_SITE', $site_id );
  }

  if( !defined( 'BLOG_ID_CURRENT_SITE' ) ) {
    define( 'BLOG_ID_CURRENT_SITE', $blog_id );
  }

  if( !defined( 'PATH_CURRENT_SITE' ) ) {
    define( 'PATH_CURRENT_SITE', $current_blog->path );
  }

  $current_site          = $wpdb->get_row( "SELECT * from {$wpdb->site} WHERE id = '{$current_blog->site_id}' LIMIT 0,1" );
  $current_site->blog_id = $wpdb->get_var( "SELECT blog_id FROM {$wpdb->blogs} WHERE domain='{$current_site->domain}' AND path='{$current_site->path}'" );
  $current_site->host    = $_host;

  if( function_exists( 'get_current_site_name' ) ) {
    $current_site = get_current_site_name( $current_site );
  }

  define( 'DOMAIN_MAPPING', 1 );
}


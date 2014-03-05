<?php
/**
 * Plugin Name: Multisite Domain Mapping Handler
 * Version: 1.2.0
 * Description: Handles database for WP-Cluster.
 * Author: Usability Dynamics
 * Domain Path: WIP
 * Network: True
 *
 * SELECT blog_id FROM corporate_blogs WHERE domain IN ('www.udx.io','udx.io') ORDER BY CHAR_LENGTH(domain) DESC LIMIT 1
 * Manually going to origin.udx.io will redirect to udx.io while www.origin.udx.io will redirect to www.udx.io.
 *
 * $current_blog->domain      => usabilitydyamics.com
 * $current_blog->subdomain   => media|static|assets
 *
 * @version 0.4.2
 */

// Disable caching to avoid errors being cached by CloudFront.
nocache_headers();

if( !defined( 'SUNRISE_LOADED' ) ) {
  define( 'SUNRISE_LOADED', 1 );
}

if( defined( 'COOKIE_DOMAIN' ) ) {
  header( 'HTTP/1.1 500 Internal Server Error' );
  wp_die( '<h1>Network Error</h1><p>The constant "COOKIE_DOMAIN" is defined (probably in wp-config.php). Please remove or comment out that define() line.</p>' );
}

$_host = $_SERVER[ 'HTTP_HOST' ];

// Enable HTTPS Setting if proxied from Nginx.
if( isset( $_SERVER[ 'HTTP_X_FORWARDED_PROTO' ] ) ) {
  $_SERVER[ 'HTTPS' ] = 'on';
}

// Amazon CloudFront gets access.
if( isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) && $_SERVER[ 'HTTP_USER_AGENT' ] === 'Amazon CloudFront' ) {}

// Veneer API Proxy Gets gets access.
if( isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) && $_SERVER[ 'HTTP_USER_AGENT' ] === 'Veneer' ) {}

// Get $current_blog unless already set (by db.php)
if( isset( $current_blog ) || ( function_exists( 'identify_current_network' ) && $current_blog = identify_current_network() ) ) {

  // Define globals.
  $blog_id = $current_blog->blog_id;
  $site_id = $current_blog->site_id;

  // Determine if extra subdomain exists.
  if( $_SERVER[ 'HTTP_HOST' ] != $subdomain = str_replace( '.' . $current_blog->domain, '', $_SERVER[ 'HTTP_HOST' ] ) ) {
    $current_blog->subdomain = $subdomain;
  }

  // Unsupported Subdomain, redirect to primary domain.
  if( isset( $current_blog->subdomain ) && !in_array( $current_blog->subdomain, array( 'secure', 'cdn', 'media', 'assets', 'static' ) ) ) {

    if( isset( $_SERVER[ 'HTTPS' ] ) ) {
      header( "Location: https://{$current_blog->domain}{$_SERVER['REQUEST_URI']}" );
    } else {
      header( "Location: https://{$current_blog->domain}{$_SERVER['REQUEST_URI']}" );
    }

    die();
  }

  // Add cookie with subdomain support.
  if( !defined( 'COOKIE_DOMAIN' ) ) {
    define( 'COOKIE_DOMAIN', $current_blog->domain );
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

  if( !$current_site = $wpdb->get_row( "SELECT * from {$wpdb->site} WHERE id = '{$current_blog->site_id}' LIMIT 0,1" ) ) {
    wp_die( 'Unable to determine network.' );
  }

  $current_site->blog_id = $wpdb->get_var( "SELECT blog_id FROM {$wpdb->blogs} WHERE domain='{$current_site->domain}' AND path='{$current_site->path}'" );
  $current_site->host    = $_host;

  if( function_exists( 'get_current_site_name' ) ) {
    $current_site = get_current_site_name( $current_site );
  }


  // Domain mapping has been successful.
  define( 'DOMAIN_MAPPING', 1 );

  return;

}

//die($wpdb->last_query);
header( 'HTTP/1.1 404 Not Found' );
wp_die( '<h1>Network Error</h1><p>The domain you requested (' . $_host . ') is not available on network.</p>' );



<?php
/**
 * Multisite Domain Mapping Handler
 *
 * SELECT blog_id FROM corporate_blogs WHERE domain IN ('www.udx.io','udx.io') ORDER BY CHAR_LENGTH(domain) DESC LIMIT 1
 *
 * Going to www.udx.io in browser will end up being here as apex-origin.udx.io given the way CF is configured.
 * To handle www.udx.io -> udx.io redirection extra logic will be nesarry - right now both will work.
 *
 * Manually going to apex-origin.udx.io will redirect to udx.io.
 *
 * @version 0.4.1
 */

// Disable caching to avoid errors being cached by CloudFront.
header( "Cache-Control: no-cache" );

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

$_host = $_SERVER[ 'HTTP_HOST' ];

// Amazon CloudFront gets access.
if( $_SERVER['HTTP_USER_AGENT'] === 'Amazon CloudFront' ) {
  $_host = str_replace( 'www-origin.', 'www.', $_host );
  $_host = str_replace( 'apex-origin.', '', $_host );
}

// Veneer API Proxy Gets gets access.
if( $_SERVER['HTTP_USER_AGENT'] === 'Veneer' ) {
  $_host = str_replace( 'www-origin.', 'www.', $_host );
  $_host = str_replace( 'apex-origin.', '', $_host );
}

// Uncaught origin request - strip away possible origins and forward to public primary.
if( strpos( $_host, '-origin.' ) > 0 ) {
  $_host = str_replace( 'www-origin.', 'www.', $_host );
  $_host = str_replace( 'apex-origin.', '', $_host );
  header( "Location: http://{$_host}{$_SERVER['REQUEST_URI']}" );
  exit();
}

// Strip Known Subdomains
$_host = str_replace( array( 'static.', 'assets.', 'media.', 'public.' ), '', $_host );

// Lookup both versions, returning the longer.
if( ( $nowww = preg_replace( '|^www\.|', '', $_host ) ) != $_host )
  $where = $wpdb->prepare( 'domain IN (%s,%s)', $_host, $nowww );
else
  $where = $wpdb->prepare( 'domain = %s', $_host );

// Order by char length in case of multiple results essentially gives the longer domain more prevelance.  
if( $current_blog = $wpdb->get_row( "SELECT * FROM {$wpdb->blogs} WHERE {$where} ORDER BY CHAR_LENGTH(domain) DESC LIMIT 1" ) ) {

  // Define globals.
  $blog_id              = $current_blog->blog_id;
  $site_id              = $current_blog->site_id;

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

  $current_site          = $wpdb->get_row( "SELECT * from {$wpdb->site} WHERE id = '{$current_blog->site_id}' LIMIT 0,1" );
  $current_site->blog_id = $wpdb->get_var( "SELECT blog_id FROM {$wpdb->blogs} WHERE domain='{$current_site->domain}' AND path='{$current_site->path}'" );
  $current_site->host    = $_host;

  if( function_exists( 'get_current_site_name' ) ) {
    $current_site = get_current_site_name( $current_site );
  }

  // Domain mapping has been successful.
  define( 'DOMAIN_MAPPING', 1 );

  return;

}

header( "Status: 404 Not Found" );
wp_die( '<h1>Network Error</h1><p>The domain you requested (' . $_host . ') is not available on network. This is nginx.</p>' );



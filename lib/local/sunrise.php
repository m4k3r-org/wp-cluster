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
  wp_die( '<h1>Network Error</h1><p>The constant "COOKIE_DOMAIN" is defined (probably in wp-config.php). Please remove or comment out that define() line.</p>' );
}

if( !count( $wpdb->get_col( "SHOW TABLES" ) ) ) {
  wp_die( '<h1>Network Error</h1><p>The network database is not setup.</p>' );
}

$_host = str_replace( '.loc', '.com', $_SERVER[ 'HTTP_HOST' ] );

// Strip Known Subdomains
$_host = str_replace( 'www.', '', $_host  );
$_host = str_replace( 'static.', '', $_host  );
$_host = str_replace( 'media.', '', $_host  );
$_host = str_replace( 'api.', '', $_host  );

if( ( $nowww = preg_replace( '|^www\.|', '', $_host ) ) != $_host )
  $where = $wpdb->prepare( 'domain IN (%s,%s)', $_host, $nowww );
else
  $where = $wpdb->prepare( 'domain = %s', $_host );

$domain_mapping_id = $wpdb->get_var( "SELECT blog_id FROM {$wpdb->blogs} WHERE {$where} ORDER BY CHAR_LENGTH(domain) DESC LIMIT 1" );

if( !$domain_mapping_id ) {
  wp_die( '<h1>Network Error</h1><p>The domain you requested (' .  $_host . ') is not available on network.</p>' );
}

if( $domain_mapping_id ) {
  $current_blog         = $wpdb->get_row( "SELECT * FROM {$wpdb->blogs} WHERE blog_id = '$domain_mapping_id' LIMIT 1" );
  $current_blog->domain = $_host;
  $current_blog->path   = '/';
  $blog_id              = $domain_mapping_id;
  $site_id              = $current_blog->site_id;

  // Add cookie with subdomain support
  define( 'COOKIE_DOMAIN', '.' . $_host );
  define( 'DOMAIN_CURRENT_SITE', $current_blog->domain );
  define( 'SITE_ID_CURRENT_SITE', $site_id );
  define( 'BLOG_ID_CURRENT_SITE', $blog_id );
  define( 'PATH_CURRENT_SITE', $current_blog->path );

  $current_site          = $wpdb->get_row( "SELECT * from {$wpdb->site} WHERE id = '{$current_blog->site_id}' LIMIT 0,1" );
  $current_site->blog_id = $wpdb->get_var( "SELECT blog_id FROM {$wpdb->blogs} WHERE domain='{$current_site->domain}' AND path='{$current_site->path}'" );
  $current_site->host    = $_host;

  if( function_exists( 'get_current_site_name' ) ) {
    $current_site = get_current_site_name( $current_site );
  }

  define( 'DOMAIN_MAPPING', 1 );
}


<?php
/**
 * Plugin Name: WP-Cluster Database
 * Version: 1.2.1
 * Description: Handles database for WP-Cluster.
 * Author: Usability Dynamics
 * Domain Path: WIP
 * Network: True
 *
 * Everything after "muplugins_loaded" action will have access to network-specific database.
 *
 * Constants are set at this point.
 */
global $current_blog, $table_prefix, $cdb;

/**
 * Connect to Cluster Database.
 *
 * @return wpdb
 */
function require_cluster_db() {
  global $wpdb, $cdb, $table_prefix;

  $_table_prefix = $table_prefix;
  if( !defined( 'CLUSTER_USER' ) ) {
    wp_die( 'Unable to connect to cluster database.' );
  }

  // Preverse $wpdb, although typically not set yet.
  $_wpdb = $wpdb;

  // Create Cluster Database Instance.
  $wpdb = new wpdb( CLUSTER_USER, CLUSTER_PASSWORD, CLUSTER_NAME, CLUSTER_HOST );

  // Output error if unable to establish connection.
  if( !$wpdb->ready ) {
    wp_die('<h1>WordPress Cloud Error</h1><p>Unable to establsh connection to ' . CLUSTER_NAME . '.</p>');
  }

  if( defined( 'CLUSTER_PREFIX' ) ) {
    $table_prefix = CLUSTER_PREFIX;
  }

  // Set Cluster Database's table references.
  wp_set_wpdb_vars();

  // Copy Cluster DB to own global variable.
  $cdb = $wpdb;

  // Revert $wpdb.
  $wpdb = $_wpdb;

  $table_prefix = $_table_prefix;

  return $cdb;

}

/**
 * Identify Current Blog.
 *
 * Used to get database information of network.
 * Sets database configuration constants.
 *
 * @return mixed
 */
function identify_current_network() {
  global $cdb, $current_blog, $wpdb, $table_prefix, $blog_id;

  $_domains = array();

  // Build domain lookup query.
  foreach( $_parts = (array) explode( '.', $_SERVER[ 'HTTP_HOST' ] ) as $index => $_domain ) {

    if( !in_array( $_domain, array( 'origin', 'net', 'com', 'io' ) ) ) {
      $_domains[] = 'www.' . implode( '.', array_slice( $_parts, $index ) );
      $_domains[] = implode( '.', array_slice( $_parts, $index ) );
    }

  };

  // Lookup blog.
  $_blog = $cdb->get_row( "SELECT * FROM {$cdb->blogs} WHERE " . $cdb->prepare( 'domain IN ("' . implode( '","', array_unique( $_domains ) ) . '")', '' ) . " ORDER BY CHAR_LENGTH(domain) DESC LIMIT 1" );

  // Blog Identified..
  if( $_blog ) {

    // Sets all table names.
    $cdb->set_blog_id( null, null );

    $network = new stdClass();
    $network->id = $_blog->site_id;

    if( !defined( 'DB_USER' ) && defined( 'CLUSTER_USER' ) ) {
      define( 'DB_USER', CLUSTER_USER );
    }

    if( !defined( 'DB_PASSWORD' ) && defined( 'CLUSTER_PASSWORD' ) ) {
      define( 'DB_PASSWORD', CLUSTER_PASSWORD );
    }

    if( !defined( 'DB_NAME' ) && ( defined( 'CLUSTER_NETWORK_NAME' ) || defined( 'CLUSTER_NAME' ) ) ) {
      define( 'DB_NAME', ( defined( 'CLUSTER_NETWORK_NAME' ) ? CLUSTER_NETWORK_NAME : CLUSTER_NAME . '_' ) . $network->id );
    }

    if( !defined( 'DB_HOST' ) && defined( 'CLUSTER_HOST' ) ) {
      define( 'DB_HOST', CLUSTER_HOST );
    }

    if( !defined( 'DB_PREFIX' ) ) {
      define( 'DB_PREFIX', 'site_' );
    }

    $table_prefix = DB_PREFIX;

    /**
     * Add Cluster Filter
     *
     * Replaces
     * users, usermeta
     * blogs, signsup, site, sitemeta, sitecategories, registration_log, blog_versions, sitecategories
     *
     *
     * SELECT * FROM site_usermeta WHERE user_id IN (1) -> SELECT * FROM cluster.cluster_usermeta WHERE user_id IN
     *
     * @note sitecategories should not be cluster-wide, although currently disabled altogether.
     */
    add_filter( 'query', function( $query ) {
      global $cdb;

      $_tables = array_merge( $cdb->global_tables, $cdb->ms_global_tables );

      $_tables = array( 'usermeta', 'users', 'blogs', 'site', 'blog_versions' );

      foreach( array( 'usermeta', 'users' ) as $table ) {
        $query = str_replace( DB_PREFIX . $table, CLUSTER_NAME . '.' . CLUSTER_PREFIX . $table, $query );
      }

      foreach( array( 'blogs', 'site', 'blog_versions' ) as $table ) {
        $query = str_replace( DB_PREFIX . $table, CLUSTER_NAME . '.' . CLUSTER_PREFIX . $table, $query );
      }

      // If not used, fails to Insert new sites.
      $query = str_replace( '`', '',  $query );

      return $query;


    });

    // Necessary for caching to work properly.
    $blog_id = $_blog->blog_id;

    // Set global variable if not alrady set.
    if( !isset( $current_blog ) ) {
      $current_blog = $_blog;
    }

  }

  if( !defined( 'DB_USER' ) ) {
    nocache_headers();
    wp_die( '<h1>Cluster Error</h1><p>Site not configured.</p>' );
  }

  return $_blog;

}

function require_cloud_db() {
  global $cdb, $wpdb;

  if( defined( 'DB_USER' ) && defined( 'DB_PASSWORD' ) && defined( 'DB_NAME' ) ) {
    $wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, defined( 'DB_HOST' ) ? DB_HOST : 'localhost' );
  }

  if( !$wpdb || !$wpdb->ready ) {
    wp_die('<h1>WordPress Cloud Error</h1><p>Unable to establsh connection to site database.</p>');
  }

}

/**
 * Create Network Specific Database and User.
 *
 * @param null   $name
 * @param string $user
 */
function create_network_database( $name = null, $user = 'user' ) {
  global $cdb;

  $cdb->query( "CREATE DATABASE {$name}" );
  $cdb->query( "CREATE USER {$user}@localhost IDENTIFIED BY '***';" );
  $cdb->query( "GRANT ALL PRIVILEGES ON *.* TO {$user}@localhost IDENTIFIED BY '***' WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;" );
  $cdb->query( "GRANT ALL PRIVILEGES ON {$name}.* TO {$user}@localhost;" );

}

require_cluster_db();

identify_current_network();

require_cloud_db();


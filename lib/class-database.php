<?php
/**
 * Our custom mult-tenant database driver
 *
 * @author Reid Williams
 * @class UsabilityDynamics\Cluster\Database
 */

namespace UsabilityDynamics\Cluster {
  if( !class_exists( 'UsabilityDynamics\Cluster\Database' ) ){
    class Database{

      /**
       * This variable holds the global tables, that should be written/read to
       * on the cluster database
       *
       * NOTE:
       * We're including the BuddyPress tables for possible future use, they'll
       * be commented out - but don't erase them.
       */
      private $wpc_global_tables = array(
        'blogs',
        'blog_versions',
        'registration_log',
        'signups',
        'site',
        'sitecategories',
        'sitemeta',
        'usermeta',
        'users' /**
        'bp_activity_sitewide',
        'bp_activity_user_activity',
        'bp_activity_user_activity_cached',
        'bp_friends',
        'bp_groups',
        'bp_groups_groupmeta',
        'bp_groups_members',
        'bp_groups_wire',
        'bp_messages_messages',
        'bp_messages_notices',
        'bp_messages_notices',
        'bp_messages_recipients',
        'bp_messages_threads',
        'bp_messages_threads',
        'bp_notifications',
        'bp_user_blogs',
        'bp_user_blogs_blogmeta',
        'bp_user_blogs_comments',
        'bp_user_blogs_posts',
        'bp_xprofile_data',
        'bp_xprofile_fields',
        'bp_xprofile_groups',
        'bp_xprofile_wire',
        'bp_activity',
        'bp_activity_meta' */
      );

      /**
       * Holds the db by reference
       */
      private $db;

      /**
       * Our current credentials
       */
      private $creds = array(
        'DB_NAME' => false,
        'DB_PREFIX' => false,
        'DB_USER' => false,
        'DB_PASSWORD' => false,
        'DB_HOST' => false
      );

      /**
       * Connects to a DB, possibly overriding the $wpdb object
       *
       * @param array $creds The DB info we're connecting with
       * @returns boolean Whether or not it was successful
       * @throws exception
       */
      function _connect_to_db( $creds ){
        global $wpdb, $table_prefix;
        /** Backup those objects */
        $backups = array(
          'wpdb' => $wpdb,
          'table_prefix' => $table_prefix,
          'creds' => $this->creds
        );
        /** Make sure we have all the creds we need */
        foreach( array_keys( $this->creds ) as $key ){
          if( !( isset( $creds[ $key ] ) && !empty( $creds[ $key ] ) ) ){
            throw new \Exception( 'Not enough credentials to connect to DB' );
          }
        }
        /** Overwrite the globals */
        $table_prefix = $creds[ 'DB_PREFIX' ];
        /** Create the DB */
        $wpdb = new \wpdb( $creds[ 'DB_USER' ], $creds[ 'DB_PASSWORD' ], $creds[ 'DB_NAME' ], $creds[ 'DB_HOST' ] );
        /** Make sure we have a connection */
        if( !( $wpdb && is_object( $wpdb ) && $wpdb->ready ) ){
          throw new \Exception( 'Invalid credentials, cannot connect to DB' );
        }
        /** Ok, we made it, lets run the func to generate the table names */
        wp_set_wpdb_vars();
        /** Ok, we made it - bail out the backups */
        foreach( $backups as $key => $value ){
          unset( $backups[ $key ] );
        }
        /** Set the 'this' variable as well */
        $this->db =& $wpdb;
        $this->creds = $creds;
        /** Return true */
        return true;
      }

      /**
       * Sanitizes select query's table names by adding database prefixes.
       *
       * This function solves the issue when we have JOINs in a select query,
       * which connects global tables from global database. For instance:
       *
       * SELECT * FROM {$wpdb->posts} AS p LEFT JOIN {$wpdb->users} AS u ON u.ID = p.post_author WHERE p.ID = 2;
       *
       * @author WPMU Plugins, Multi-DB Plugin
       * @since 3.1.2
       * @filter query
       *
       * @access public
       * @global array $global_tables The array of globals tables.
       * @param string $query The initial query.
       * @return string Sanitized query.
       */
      public function _filter_query( $query ) {
        global $blog_id;
        $global_tables = $this->wpc_global_tables;
        /** Trim it up */
        $query = trim( $query );
        /** Here, I'm going to strip out the "_##_ pattern where that number represents the blog ID */
        if( stripos( $query, '_' . $blog_id . '_' ) ){
          $query = preg_replace( '/' . DB_PREFIX . $blog_id . '_([^ ]*)/i', DB_PREFIX . '$1', $query );
        }
        /** Don't touch non select queries past this point */
        if ( !preg_match( '/^SELECT\s+/is', $query ) ) {
          return $query;
        }
        /** Look through all global tables and add global database prefix if it has been found */
        foreach ( $global_tables as $table ) {
          $query = preg_replace( '/' . DB_PREFIX . $table . '/i', CLUSTER_NAME . '.' . CLUSTER_PREFIX . $table, $query );
        }
        /** Return it! */
        return $query;
      }

      /**
       * Returns all tables available in the database.
       *
       * @filter _filter_tables_to_repair
       * @todo Do this, not sure when this is called!
       * @access public
       * @param array $tables The initial array of tables.
       * @return array The array of database tables.
       */
      public function _filter_tables_to_repair( $tables ) {
        return $tables;
        /** Haven't changed anything below this line yet - williams@ud */
        $blogs_ids = $this->get_col( "SELECT blog_id FROM {$this->base_prefix}blogs WHERE deleted = 0 AND spam = 0 AND archived = '0'" );
        foreach ( $blogs_ids as $blog_id ) {
          $new_tables = $this->get_col( "SHOW TABLES LIKE '{$this->base_prefix}{$blog_id}_%';" );
          if ( $new_tables && is_array( $new_tables ) && count( $new_tables ) > 0 ) {
            $tables = array_merge( $tables, $new_tables );
          }
        }
        return $tables;
      }

      /**
       * Identify Current Blog.
       *
       * Used to get database information of network.
       * Sets database configuration constants.
       *
       * @return mixed
       * @throws exception
       */
      function _identify_current_network() {
        /** Bring in our globals */
        global $current_blog, $blog_id, $wp_cluster;
        /** Try to figure out our domain parts and reconstruct them */
        $possible_domains = array();
        $domain_parts = (array) explode( '.', $_SERVER[ 'HTTP_HOST' ] );
        /** Add our domain suffixes */
        $domain_suffix = array_pop( $domain_parts );
        /** Now, lets try to figure out the different domains we may have available, by going backwards */
        $last_domain = $domain_suffix;
        for( $x = count( $domain_parts ) - 1; $x >= 0; $x-- ){
          $last_domain = $domain_parts[ $x ] . '.' . $last_domain;
          $possible_domains[] = $last_domain;
        }
        /** Try to lookup the blog */
        $query = "SELECT * FROM {$this->db->blogs} WHERE " . $this->db->prepare( 'domain IN ("' . implode( '","', array_unique( $possible_domains ) ) . '")', '' ) . " ORDER BY CHAR_LENGTH( domain ) DESC LIMIT 1";
        $blog = $this->db->get_row( $query );
        /** If we don't have a blog, bail */
        if( !$blog ){
          throw new Exception( 'Could not find site for: ' . $_SERVER[ 'HTTP_HOST' ] );
        }
        /** Set the blog ID */
        $this->db->set_blog_id( null, null );
        /** Necessary for caching to work properly */
        $blog_id = $blog->blog_id;
        /** Set global variable if not already set */
        if( !isset( $current_blog ) ) {
          $current_blog = $blog;
        }
        return $blog;
      }

      /**
       * Initializes the databases, actions, and filters for this to properly operate
       * @param bool $do_stuff Whether we should actually do initialization( needed for 'init' )
       */
      function __construct( $do_stuff = true ){
        global $wp_cluster;
        if( !( is_bool( $do_stuff ) && $do_stuff ) ){
          return;
        }
        /** First thing we're going to do, is connect to the cluster db */
        $creds = array(
          'DB_NAME' => ( defined( 'CLUSTER_NAME' ) ? CLUSTER_NAME : false ),
          'DB_PREFIX' => ( defined( 'CLUSTER_PREFIX' ) ? CLUSTER_PREFIX : false ),
          'DB_USER' => ( defined( 'CLUSTER_USER' ) ? CLUSTER_USER : false ),
          'DB_PASSWORD' => ( defined( 'CLUSTER_PASSWORD' ) ? CLUSTER_PASSWORD : false ),
          'DB_HOST' => ( defined( 'CLUSTER_HOST' ) ? CLUSTER_HOST : false )
        );
        /** Attempt to connect with these creds */
        $this->_connect_to_db( $creds );
        /** Now, try to identify the current network */
        $blog = $this->_identify_current_network();
        /** Get our config info */
        /** @todo We should really be getting this from the DB */
        $blog_db_options = $wp_cluster->config->get_config( 'options/domains/' . $blog->domain, 'db' );
        /** If we have our blog options, lets go ahead and set them up */
        if( $blog_db_options ){
          /** Loop through them and define the vars */
          foreach( $blog_db_options as $key => $value ){
            if( !defined( $key ) ){
              define( $key, $value );
            }
          }
        }
        /** Ok, we should have our DB info setup now */
        $creds = array(
          'DB_NAME' => ( defined( 'DB_NAME' ) ? DB_NAME : false ),
          'DB_PREFIX' => ( defined( 'DB_PREFIX' ) ? DB_PREFIX : false ),
          'DB_USER' => ( defined( 'DB_USER' ) ? DB_USER : false ),
          'DB_PASSWORD' => ( defined( 'DB_PASSWORD' ) ? DB_PASSWORD : false ),
          'DB_HOST' => ( defined( 'DB_HOST' ) ? DB_HOST : false )
        );
        /** Attempt to connect with these creds */
        $this->_connect_to_db( $creds );
        /** Well, the only thing left to do is create those actions */
        add_filter( 'tables_to_repair', array( $this, '_filter_tables_to_repair' ) );
        add_filter( 'query', array( $this, '_filter_query' ) );
      }

      /**
       * This function lets us chain methods without having to instantiate first, YOU MUST COPY THIS TO ALL SUB CLASSES
       */
      static public function init(){
        return new self( false );
      }

    }
  }
}
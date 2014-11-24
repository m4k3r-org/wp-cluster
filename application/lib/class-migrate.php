<?php
/**
 * Handles the DDP migration, here are the steps that should be run when going through
 * the migration process
 *
 * 1) Update the 'reid' user to be xadministrator in the DB directly
 * 2) Via commandline, run "wp --url=discodonniepresents.com ddp migrate"
 * 3) Delete the QA tables
 */

namespace EDM\Application {

  if( !class_exists( 'Migration ' ) ){
    class Migration{

      /**
       * Holds old data
       */
      protected $old_data = array();

      /**
       * Holds the result of our migration
       */
      protected $new_data = array();

      /**
       * Holds our logs if they're not printed out
       */
      public $logs = array();

      /**
       * If we should display the logs immediately
       */
      public $dump_logs = false;

      /**
       * Holds any cache information we want
       */
      protected $cache = array();

      /**
       * Holds my schemas
       */
      protected $schemas = array();

      /**
       * Holds the current stage
       */
      protected $stage = false;

      /**
       * Holds our current state
       */
      protected $state = array();

      /**
       * Holds the data types we're going to convert
       */
      protected $data_types_map = array(
        /***********************************
         * POST TYPES
         ***********************************/
        /** Done */
        'event' => array(
          'old_slug' => 'hdp_event',
          'new_slug' => 'event',
          'old_type' => 'post',
          'new_type' => 'post',
          'meta_name' => 'event',
          'meta' => array(
            'rename' => array(
              '_thumbnail_id' => 'posterImage',
              'hdp_purchase_url' => 'urlTicket',
              'hdp_facebook_rsvp_url' => 'urlRsvp'
            ),
            'drop' => array(
              'city',
              'country',
              'country_code',
              'county',
              'district',
              'formatted_address',
              'geo_hash',
              'hdp_event_date',
              'hdp_event_meta_date',
              'hdp_event_meta_facebook_url',
              'hdp_event_meta_purchase_url',
              'hdp_event_meta_time',
              'hdp_event_time',
              'latitude',
              'location_type',
              'longitude',
              'postal_code',
              'precision',
              'route',
              'state',
              'state_code',
              'street_number'
            )
          ),
          'terms' => array(
            'convert' => array(
              'hdp_tour' => 'tour',
              'hdp_venue' => 'venue',
              'hdp_promoter' => 'promoters',
              'hdp_artist' => 'artists',
              'hdp_type' => 'event-type'
            )
          )
        ),
        'imageGallery' => array(
          'new_slug' => 'imageGallery',
          'old_slug' => 'hdp_photo_gallery',
          'old_type' => 'post',
          'new_type' => 'post',
          'meta' => array(
            'rename' => array(
              '_thumbnail_id' => 'primaryImageOfPage',
              'hdp_facebook_url' => 'isBasedOnUrl'
            ),
            'drop' => array(
              'city',
              'country',
              'country_code',
              'county',
              'district',
              'formatted_address',
              'geo_hash',
              'hdp_event_date',
              'hdp_event_time',
              'hdp_poster_id',
              'latitude',
              'location_type',
              'longitude',
              'postal_code',
              'precision',
              'route',
              'state',
              'state_code',
              'street_number'
            )
          ),
          'terms' => array(
            'convert' => array(
              'credit' => 'credit'
            )
          )
        ),
        'videoObject' => array(
          'new_slug' => 'videoObject',
          'old_slug' => 'hdp_video',
          'old_type' => 'post',
          'new_type' => 'post',
          'meta_name' => 'video',
          'meta' => array(
            'rename' => array(
              'hdp_video_url' => 'isBasedOnUrl'
            ),
            'drop' => array(
              'city',
              'country',
              'country_code',
              'county',
              'formatted_address',
              'geo_hash',
              'hdp_event_date',
              'hdp_poster_id',
              'latitude',
              'location_type',
              'longitude',
              'postal_code',
              'post_tagline',
              'precision',
              'route',
              'state',
              'state_code',
              'street_number',
              '_thumbnail_id'
            )
          ),
          'terms' => array(
            'convert' => array(
              'credit' => 'credit'
            )
          )
        ),
        /***********************************
         * TERMS
         ***********************************/
        /** Done */
        'category' => array(
          'old_slug' => 'category',
          'new_slug' => 'category',
          'old_type' => 'term',
          'new_type' => 'term'
        ),
        'credit' => array(
          'old_slug' => 'credit',
          'new_slug' => 'credit',
          'old_type' => 'enhanced_term',
          'new_type' => 'post',
          'meta_name' => 'creator',
          'meta' => array(
            'rename' => array(
              'hdp_facebook_url' => 'socialLinks',
              'hdp_google_plus_url' => 'socialLinks',
              'hdp_twitter_url' => 'socialLinks',
              'hdp_website_url' => 'officialLink',
              'hdp_youtube_url' => 'socialLinks',
              '_thumbnail_id' => 'logo'
            ),
            'drop' => array(
              'extended_term_id',
              'extended_term_taxonomy'
            )
          )
        ),
        /** Done */
        'age_limit' => array(
          'old_slug' => 'hdp_age_limit',
          'new_slug' => 'age-limit',
          'old_type' => 'enhanced_term',
          'new_type' => 'term'
        ),
        /** Done */
        'artist' => array(
          'old_slug' => 'hdp_artist',
          'new_slug' => 'artist',
          'old_type' => 'enhanced_term',
          'new_type' => 'post',
          'meta_name' => 'artists',
          'meta' => array(
            'rename' => array(
              'hdp_facebook_url' => 'socialLinks',
              'hdp_google_plus_url' => 'socialLinks',
              'hdp_twitter_url' => 'socialLinks',
              'hdp_website_url' => 'officialLink',
              'hdp_youtube_url' => 'socialLinks',
              '_thumbnail_id' => 'headshotImage'
            ),
            'drop' => array(
              'extended_term_id',
              'extended_term_taxonomy',
              'facebook_url',
              'twitter',
              'website_url'
            )
          )
        ),
        /** Done */
        'city' => array(
          'old_slug' => 'hdp_city',
          'new_slug' => 'city',
          'old_type' => 'enhanced_term',
          'new_type' => 'term'
        ),
        /** Done */
        'genre' => array(
          'old_slug' => 'hdp_genre',
          'new_slug' => 'genre',
          'old_type' => 'enhanced_term',
          'new_type' => 'term'
        ),
        /** Done */
        'promoter' => array(
          'old_slug' => 'hdp_promoter',
          'new_slug' => 'promoter',
          'old_type' => 'enhanced_term',
          'new_type' => 'post',
          'meta_name' => 'promoters',
          'meta' => array(
            'rename' => array(
              'hdp_facebook_url' => 'socialLinks',
              'hdp_google_plus_url' => 'socialLinks',
              'hdp_twitter_url' => 'socialLinks',
              'hdp_website_url' => 'officialLink',
              'hdp_youtube_url' => 'socialLinks',
              '_thumbnail_id' => 'logoImage'
            ),
            'drop' => array(
              'city',
              'country',
              'country_code',
              'county',
              'extended_term_id',
              'extended_term_taxonomy',
              'facebook_url',
              'formatted_address',
              'latitude',
              'longitude',
              'postal_code',
              'route',
              'state',
              'state_code',
              'street_number',
              'twitter',
              'website_url'
            )
          )
        ),
        /** Done */
        'state' => array(
          'old_slug' => 'hdp_state',
          'new_slug' => 'state',
          'old_type' => 'enhanced_term',
          'new_type' => 'term'
        ),
        /** Done */
        'tour' => array(
          'old_slug' => 'hdp_tour',
          'new_slug' => 'tour',
          'old_type' => 'enhanced_term',
          'new_type' => 'post',
          'meta_name' => 'tour',
          'meta' => array(
            'rename' => array(
              '_thumbnail_id' => 'posterImage'
            ),
            'drop' => array(
              'extended_term_id',
              'extended_term_taxonomy',
            )
          )
        ),
        /** Done */
        'type' => array(
          'old_slug' => 'hdp_type',
          'new_slug' => 'event-type',
          'old_type' => 'enhanced_term',
          'new_type' => 'term'
        ),
        /** Done */
        'venue' => array(
          'old_slug' => 'hdp_venue',
          'new_slug' => 'venue',
          'old_type' => 'enhanced_term',
          'new_type' => 'post',
          'meta_name' => 'venue',
          'meta' => array(
            'rename' => array(
              'formatted_address' => 'locationAddress',
              'hdp_website_url' => 'officialLink',
              'hdp_facebook_url' => 'socialLinks',
              'hdp_youtube_url' => 'socialLinks',
              'hdp_twitter_url' => 'socialLinks',
              'hdp_google_plus_url' => 'socialLinks',
              '_thumbnail_id' => 'imageLogo'
            ),
            'drop' => array(
              'city',
              'country',
              'country_code',
              'county',
              'district',
              'extended_term_id',
              'extended_term_taxonomy',
              'geo_hash',
              'hdp_event_date',
              'hdp_event_time',
              'hdp_facebook_rsvp_url',
              'hdp_purchase_url',
              'latitude',
              'location_type',
              'longitude',
              'postal_code',
              'precision',
              'route',
              'state',
              'state_code',
              'street_number'
            )
          )
        )
      );

      /**
       * Holds our common maps
       */
      protected $common_maps = array(
        'source' => array(
          'enhanced_term' => array(
            'name',
            'slug',
            'description',
            '_terms'
          ),
          'post' => array(
            'post_title',
            'post_name',
            'post_content'
          ),
          'term' => array(
            'term_taxonomy_id',
            'term_id',
            'description',
            'parent',
            'count'
          )
        ),
        'destination' => array(
          'post' => array(
            'post_title',
            'post_name',
            'post_content',
            '_terms'
          ),
          'term' => array(
            'term_taxonomy_id',
            'term_id',
            'description',
            'parent',
            'count'
          )
        )
      );

      /**
       * Our constructor, just kicks things off
       *
       * @param mixed $stage The stage we want to perform (i.e. artist for just artist)
       */
      function __construct( $stage = false ){
        global $wpdb, $current_blog;

        if( !current_user_can( 'manage_options' ) ) {
          wp_die('go away');
        }

        /** Clear the buffer */
        while( ob_get_level() ){
          ob_end_flush();
        }
        ob_implicit_flush( 1 );

        if( isset( $_REQUEST[ 'dump_logs' ] ) ){
          $this->dump_logs = true;
        }

        /** We do this so we don't actually save anything */
        $wpdb->query( 'SET autocommit = 0' );
        $wpdb->query( 'START TRANSACTION' );

        /** If we have a get variable, we override stage */
        if( isset( $_REQUEST[ 'stage' ] ) && $_REQUEST[ 'stage' ] ){
          $this->stage = $_REQUEST[ 'stage' ];
        }elseif( $stage ){
          $this->stage = $stage;
        }

        /** Ok, lets see if we have a state file */
        if( file_exists( '.state' ) ){
          $this->state = json_decode( file_get_contents( '.state' ), ARRAY_A );
        }

        /** Bring in our schemas */
        $this->_echo( "Loading schemas from vertical-edm..." );
        $this->_load_schemas();

        /** Going to attempt to generate our maps */
        $this->_echo( "Generating field maps..." );
        $this->_generate_maps();

        /** Run some tests */
        $this->_run_tests();

        /** Now, go through and convert the enhanced terms */
        $this->convert_enhanced_terms();
        
        /** Now, go through and convert the posts */
        $this->convert_posts();

        /** Now, rename our taxonomies */
        $this->rename_taxonomies();

        /** Ok, we're done (yay) - now do cleanup */
        $this->do_cleanup();

        /**Now do our system refresh */
        $this->upgrade_system();

        /** Ok commit our changes now */
        $wpdb->query( 'COMMIT' );

        /** We're done */
        $this->_echo( "Done!" );
      }

      /**
       * This is just a helper function to run a test if we need to get certain types of data,
       * based on the _GET variable in the URL
       */
      function _run_tests( $test = false, $args = array() ){
        global $wpdb;
        $data = false;
        $_test = false;
        if( isset( $_GET[ 'test' ] ) && $_GET[ 'test' ] ){
          $_test = $_GET[ 'test' ];
        }
        if( $test ){
          $_test = $test;
        }
        /** Ok, get some new args now */
        $_args = (array) \UsabilityDynamics\Utility::parse_args( $args, $_GET );
        $__GET = $_GET;
        $_GET = $_args;
        if( $_test ){
          switch( $_test ){
            case 'enhanced_terms':
              $data = $wpdb->get_col( "SELECT DISTINCT post_type FROM {$wpdb->posts} WHERE post_type like '_tp_hdp_%' ORDER BY post_type ASC" );
              break;
              case 'taxonomy':
              $query = "SELECT DISTINCT taxonomy FROM {$wpdb->term_taxonomy} ORDER BY taxonomy ASC";
              $data = $wpdb->get_col( $query );
              break;
            case 'post_schema_meta':
              $post_type = isset( $_GET[ 'post_type' ] ) && is_string( $_GET[ 'post_type' ] ) ? $_GET[ 'post_type' ] : 'null';
              $data = @$this->schemas[ $post_type ][ 'meta' ];
              break;
            case 'post_type_meta':
              $data = array();
              foreach( $this->data_types_map as $key => $value ){
                $post_type = $value[ 'old_slug' ];
                if( $value[ 'old_type' ] == 'enhanced_term' ){
                  $post_type = '_tp_' . $post_type;
                }
                $data[ $post_type ] = $this->get_post_type_meta( $post_type );
                /** Now try also for the new kind */
                if( $value[ 'new_type' ] == 'post' ){
                  $data[ $key ] = $this->get_post_type_meta( $key );
                }
              }
              break;
            case 'post_types':
              $data = $wpdb->get_col( "SELECT DISTINCT post_type FROM {$wpdb->posts} ORDER BY post_type ASC" );
              break;
            case 'post_meta_keys':
              $query = "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} WHERE meta_key like 'hdp_%'";
              $data = $wpdb->get_col( $query );
              break;
            case 'post_statues':
              $query = "SELECT post_type, post_status, count( post_status ) AS 'count' FROM {$wpdb->posts} GROUP BY post_type, post_status";
              $data = $wpdb->get_results( $query, ARRAY_A );
              break;
            case 'get_post':
              $post_id = isset( $_GET[ 'post_id' ] ) && is_numeric( $_GET[ 'post_id' ] ) ? $_GET[ 'post_id' ] : 0;
              $post_type = isset( $_GET[ 'post_type' ] ) && is_string( $_GET[ 'post_type' ] ) ? $_GET[ 'post_type' ] : null;
              if( !is_null( $post_type ) ){
                $query = $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish' ORDER BY RAND() LIMIT 1", array( $post_type ) );
                $post_id = $wpdb->get_var( $query );
              }
              $post = get_post( $post_id, ARRAY_A );
              /** Create an array */
              $data = array(
                $post_id => $post
              );
              /** Add the meta and terms */
              $this->posts_add_meta( $data );
              $this->posts_add_terms( $data );
              break;
          }
        }
        $_GET = $__GET;
        if( $test ){
          return $data;
        }
        if( $data ){
          if( isset( $_REQUEST[ 'php' ] ) ){
            var_export( $data );
            die();
          }else{
            echo "<pre>";
            prq( $data );
            echo "</pre>";
          }
          die();
        }
      }

      /**
       * Load our schemas
       */
      function _load_schemas(){
        if( defined( 'WP_ELASTIC_SCHEMAS_DIR' ) ){
          $path_to_schemas = WP_ELASTIC_SCHEMAS_DIR;
        }else{
          $path_to_schemas = rtrim( WP_MODULE_DIR, '/' ) . '/vertical-edm/static/schemas';
        }
        /** Ok, we're first going to create our objects in the data array */
        foreach( $this->data_types_map as $key => $value ){
          $filename = $path_to_schemas . '/' . $key . '.json';
          /** Try to load the file */
          if( is_file( $filename ) ){
            if( !( $this->schemas[ $key ] = json_decode( file_get_contents( $filename ), ARRAY_A ) ) ){
              unset( $this->schemas[ $key ] );
            }
          }elseif( !isset( $this->schemas[ $key ] ) ){
            /** If we can't load the schema, and we're not a term, unset us */
            if( $value[ 'new_type' ] == 'post' ){
              $this->_echo( "Not converting {$key}, it needs to be a post, but there is no schema found..." );
              unset( $this->data_types_map[ $key ] );
            }
          }
        }
      }

      /**
       * Generate our maps from the schemas that are there
       */
      function _generate_maps(){
        /** Ok, loop through our data types */
        foreach( $this->data_types_map as $type => &$value ){
          /** Bail if we're a post=>post conversion */
          if( $value[ 'old_type' ] == 'post' && $value[ 'new_type' ] == 'post' ){
            continue;
          }
          /** If we have a map already defined, bring it in */
          if( isset( $value[ 'map' ] ) ){
            $map = $value[ 'map' ];
          }else{
            $map = array();
          }
          /** Startup the map */
          switch( true ){
            case $value[ 'old_type' ] == 'enhanced_term' && $value[ 'new_type' ] == 'term':
              $source_map = isset( $this->common_maps[ 'source' ][ 'term' ] ) ? $this->common_maps[ 'source' ][ 'term' ] : array();
              break;
            default:
              $source_map = isset( $this->common_maps[ 'source' ][ $value[ 'old_type' ] ] ) ? $this->common_maps[ 'source' ][ $value[ 'old_type' ] ] : array();
              break;
          }
          $destination_map = isset( $this->common_maps[ 'destination' ][ $value[ 'new_type' ] ] ) ? $this->common_maps[ 'destination' ][ $value[ 'new_type' ] ] : array();
          /** If we have our schema */
          $schema = isset( $this->schemas[ $type ] ) ? $this->schemas[ $type ] : array();
          /** Ok, try to start the map now */
          foreach( $source_map as $key => $source ){
            if( isset( $destination_map[ $key ] ) ){
              $map[ $source ] = $destination_map[ $key ];
            }
          }
          /** Ok, we finally made it, we can set our map */
          $value[ 'map' ] = $map;
        }
      }

      /**
       * Cleans up the database, removes all the old post types, and extra data that's there
       */
      function do_cleanup(){
        global $wpdb;
        /** First, remove all the old post types */
        $old_post_types = array( 'null' );
        $old_taxonomies = array( 'null' );
        /** Loop through our data types map */
        foreach( $this->data_types_map as $slug => $map ){
          if( $map[ 'old_type' ] == 'post' ){
            $old_post_types[] = $map[ 'old_slug' ];
          }
          if( $map[ 'old_type' ] == 'enhanced_term' ){
            $old_post_types[] = '_tp_'  . $map[ 'old_slug' ];
            $old_taxonomies[] = $map[ 'old_slug' ];
          }
        }
        $this->_echo( "Removing old post types..." );
        $query = "DELETE FROM {$wpdb->posts} WHERE post_type IN ( '" . implode( "', '", $old_post_types ) . "' )";
        $this->_echo( $query );
        $wpdb->query( $query );
        $this->_echo( "Removing old taxonomies..." );
        $query = "DELETE FROM {$wpdb->term_taxonomy} WHERE taxonomy IN ( '" . implode( "', '", $old_taxonomies ) . "' )";
        $this->_echo( $query );
        $wpdb->query( $query );
        /** Now, go through and get rid of all the old post meta that doesn't exist */
        $this->_echo( "Cleaning up orphans..." );
        $queries = array(
          "DELETE FROM {$wpdb->postmeta} WHERE post_id NOT IN ( SELECT ID FROM {$wpdb->posts} )",
          "DELETE FROM {$wpdb->terms} WHERE term_id NOT IN ( SELECT DISTINCT term_id FROM {$wpdb->term_taxonomy} )",
          "DELETE FROM {$wpdb->term_relationships} WHERE term_taxonomy_id NOT IN ( SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} )",
          "DELETE FROM {$wpdb->term_relationships} WHERE object_id NOT IN ( SELECT ID FROM {$wpdb->posts} )"
        );
        foreach( $queries as $query ){
          $this->_echo( $query );
          $wpdb->query( $query );
        }
        /** Ok, now remove all references to any of the genre terms */
        $query = "DELETE FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ( SELECT term_taxonomy_id from {$wpdb->term_taxonomy} WHERE taxonomy = 'genre' )";
        $this->_echo( $query );
        $wpdb->query( $query );
      }

      /**
       * Handles our general system upgrade scripts
       */
      function upgrade_system(){
        global $wpdb, $current_blog;

        $this->_echo( "Upgrading system..." );

        /** Flush any cache */
        wp_cache_flush();
        
        /** Delete global transients */
        $wpdb->query( "DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE '%_transient_%'" );

        /** Delete all the global options we don't care about */
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'elasticsearch'" );
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%_transient_%'" );
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%ud::%'" );
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%seo_woo_%'" );
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%woo_%'" );
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%cloudflare%'" );

        /** Run Site Upgrade Scripts. */
        $sites = $wpdb->get_results( "SELECT * FROM {$wpdb->blogs}" );
        $sites_plugins_processed = array();
        foreach( $sites as $site ) {
          /** Change the currently active site */
          switch_to_blog( $site->blog_id );
          $this->_echo( "Processing site {$site->site_id}, blog {$site->domain} ({$site->blog_id})..." );

          /** Flush any cache */
          wp_cache_flush();

          /** Remove old post types from flawless */
          $flawless = get_option( 'flawless_settings' );
          if( $flawless ) {
            //die( '<pre>' . print_r( $flawless, true ) . '</pre>' );
            unset( $flawless[ 'post_types' ][ 'hdp_event' ] );
            unset( $flawless[ 'post_types' ][ 'hdp_photo_gallery' ] );
            unset( $flawless[ 'post_types' ][ 'hdp_video' ] );
            unset( $flawless[ 'post_types' ][ 'attachment' ] );
            unset( $flawless[ 'post_types' ][ '_tp_post_tag' ] );
            unset( $flawless[ 'taxonomies' ][ 'hdp_tour' ] );
            unset( $flawless[ 'taxonomies' ][ 'hdp_promoter' ] );
            unset( $flawless[ 'taxonomies' ][ 'hdp_city' ] );
            unset( $flawless[ 'taxonomies' ][ 'hdp_state' ] );
            unset( $flawless[ 'taxonomies' ][ 'hdp_type' ] );
            unset( $flawless[ 'taxonomies' ][ 'hdp_age_limit' ] );
            unset( $flawless[ 'taxonomies' ][ 'hdp_genre' ] );
            unset( $flawless[ 'taxonomies' ][ 'hdp_venue' ] );
            unset( $flawless[ 'taxonomies' ][ 'hdp_artist' ] );
            unset( $flawless[ 'taxonomies' ][ 'credit' ] );
            update_option( 'flawless_settings', $flawless );
          }

          /** Update image sizes */
          update_option( 'thumbnail_size_w', 230 );
          update_option( 'thumbnail_size_h', 130 );

          /** Remove old blog specific options */
          $wpdb->query( "DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE 'elasticsearch'" );
          $wpdb->query( "DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE '%_transient_%'" );
          $wpdb->query( "DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE '%ud::%'" );
          $wpdb->query( "DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE '%seo_woo_%'" );
          $wpdb->query( "DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE '%woo_%'" );
          $wpdb->query( "DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE '%cloudflare%'" );
          $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}taxonomymeta" );
          $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}stream" );
          $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}stream_meta" );
          $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}stream_context" );
          $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ud_qa_hdp_event;" );
          $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ud_qa_hdp_photo_gallery;" );
          $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ud_qa_hdp_video;" );

          /** Get our list of plugins */
          $plugins = get_plugins();
          /** Ok, we have to go through the plugins, and activate/deactivate them as needed */
          $active_sitewide_plugins = get_site_option( 'active_sitewide_plugins', array() );
          $active_plugins = get_option( 'active_plugins', array() );
          /** First deactivate */
          if( !in_array( $site->site_id, $sites_plugins_processed ) ){
            deactivate_plugins( array_keys( $active_sitewide_plugins ), true, true );
          }
          deactivate_plugins( $active_plugins, true, false );
          /** Now, reactivate */
          if( !in_array( $site->site_id, $sites_plugins_processed ) ){
            foreach( array_keys( $active_sitewide_plugins ) as $plugin ){
              if( in_array( $plugin, array_keys( $plugins ) ) ){
                $this->_echo( "Activating plugin {$plugin} for site..." );
                activate_plugin( $plugin, '', true, true );
              }else{
                $this->_echo( "Could not find plugin {$plugin} to activate for site..." );
              }
            }
            /** Save the fact that we've processed this site */
            $sites_plugins_processed[] = $site->site_id;
          }
          foreach( $active_plugins as $plugin ){
            if( in_array( $plugin, array_keys( $plugins ) ) ){
              $this->_echo( "Activating plugin {$plugin} for blog..." );
              activate_plugin( $plugin, '', false, true );
            }else{
              $this->_echo( "Could not find plugin {$plugin} to activate for blog..." );
            }
          }

          /** Fix the upload path */
          update_option( 'upload_path', '/storage/public/' . $site->domain );

          /** Now we need to reactivate the current site's theme */
          $themes = get_theme_roots();
          $theme_map = array( 
            "flawless" => "flawless",
            "flawless-hddp" => "flawless-hddp",
            "bassodyssey" => "wp-bassoddysey",
            "dayafter" => "wp-dayafter",
            "wp-festival" => "wp-festival",
            "wp-festival-2" => "wp-festival-2",
            "freaksbeatstreats" => "wp-freaksbeatstreats",
            "hififest.com" => "wp-hififest",
            "lanmexico-2014" => "wp-lanmexico",
            "monsterblockparty.com" => "wp-monsterblockparty",
            "wp-spectacle" => "wp-spectacle",
            "wp-spectacle-2" => "wp-spectacle-2",
            "wp-spectacle-chmf" => "wp-spectacle-chmf",
            "wp-spectacle-isladelsol" => "wp-spectacle-isladelsol",
            "network-splash" => "wp-splash",
            "wp-splash" => "wp-splash",
            "thegift" => "wp-thegift",
            "winterfantasy-2" => "wp-winterfantasy",
            "braxton" => "wp-braxton",
            "k-boom" => "wp-kboom"
          );
          $stylesheet = get_option( 'stylesheet' );
          $this->_echo( "Trying to find theme called: " . $stylesheet );
          /** Convert our theme if it exists */
          if( isset( $theme_map[ $stylesheet ] ) && $theme_map[ $stylesheet ] != $stylesheet ){
            $stylesheet = $theme_map[ $stylesheet ];
            $this->_echo( "Found theme in map, renaming to {$stylesheet}." );
          }
          if( !in_array( $stylesheet, array_keys( $themes ) ) ){
            $this->_echo( "Could not find theme {$stylesheet}. Reverting to wp-splash." );
            $stylesheet = 'wp-splash';
          }
          /** Ok, go ahead and update our stylesheet root */
          /** Flush any cache */
          wp_cache_flush();
          /** Ok, setup our arguments */
          $theme = wp_get_theme( $stylesheet, WP_BASE_DIR . $themes[ $stylesheet ] );
          $template = $theme->get_template();
          /** Make sure we have access to the template */
          if( !in_array( $template, array_keys( $themes ) ) ){
            $theme = wp_get_theme( 'wp-splash' );
            $stylesheet = 'wp-splash';
            $template = 'wp-splash';
          }
          /** Alright, update all our options */
          $this->_echo( "Working with theme: " . $theme->get( 'Name' ) . "..." );
          update_option( 'current_theme', $theme->get( 'Name' ) );
          update_option( 'template', $template );
          update_option( 'template_root', $themes[ $template ] );
          update_option( 'stylesheet', $stylesheet );
          update_option( 'stylesheet_root', $themes[ $stylesheet ] );

          /** Restore the current blog */
          restore_current_blog();
        }

        /** Add our xAdmin account */
        $user = array(
          'user_pass' => 'LJzflMVaGpWV',
          'user_login' => 'xadministrator',
          'user_nicename' => 'xadministrator',
          'user_email' => 'directors@usabilitydynamics.com',
          'display_name' => 'xAdministrator',
          'role' => 'administrator'
        );
        if( username_exists( $user[ 'user_login' ] ) ){
          $user = get_user_by( 'login', $user[ 'user_login' ] );
          $user_id = $user->ID;
          $user_login = $user->user_login;
        }elseif( email_exists( $user[ 'user_email' ] ) ){
          $user = get_user_by( 'email', $user[ 'user_email' ] );
          $user_id = $user->ID;
          $user_login = $user->user_login;
        }else{
          $user_id = wp_insert_user( $user );
          $user_login = $user[ 'user_login' ];
          $this->_echo( 'Created administrator with ID of: ' . $user_id . '...' );
        }
        $this->_echo( 'Trying to grant super admin privs for user: ' . $user_id );
        $data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->sitemeta WHERE meta_key = %s", 'site_admins' ), ARRAY_A );
        /** Loop through and update the data */
        foreach( $data as $site_admins ){
          $site_admins[ 'meta_value' ] = unserialize( $site_admins[ 'meta_value' ] );
          if( !in_array( $user_login, $site_admins[ 'meta_value' ] ) ){
            $site_admins[ 'meta_value' ][] = $user_login;
            $site_admins[ 'meta_value' ] = serialize( $site_admins[ 'meta_value' ] );
            $wpdb->update( $wpdb->sitemeta, $site_admins, array( 'meta_id' => $site_admins[ 'meta_id' ] ) );
          }
        }
        $data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->sitemeta WHERE meta_key = %s", 'site_admins' ), ARRAY_A );

        /** We're done with the system upgrade */
        $this->_echo( 'Finished running the system upgrade...' );
      }

      /**
       * This function simply renames all the taxonomies
       */
      function rename_taxonomies(){
        global $wpdb;
        $this->_echo( 'Renaming taxonomis...' );
        /** Loop through and rename */
        foreach( $this->data_types_map as $type => $map ){
          /** Ok, now delete the ones that used to be terms, but they're now posts */
          if( $map[ 'old_type' ] == 'enhanced_term' && $map[ 'new_type' ] != 'term' ){
            /** Delete all the post to term relationships */
            $query = $wpdb->prepare( "
              DELETE FROM
                {$wpdb->term_relationships}
              WHERE
                term_taxonomy_id IN (
                  SELECT DISTINCT
                    term_taxonomy_id
                  FROM
                    {$wpdb->term_taxonomy}
                  WHERE
                    taxonomy = %s
                )
              ", array(
              $map[ 'old_slug' ]
            ) );
            $this->_echo( trim( preg_replace('/\s+/', ' ', $query ) ) );
            $wpdb->query( $query );
            /** Delete all the term to taxonomy relationships */
            $query = $wpdb->prepare( "
              DELETE FROM
                {$wpdb->terms}
              WHERE
                term_id IN (
                  SELECT DISTINCT
                    term_id
                  FROM
                    {$wpdb->term_taxonomy}
                  WHERE
                    taxonomy = %s
                )
              ", array(
              $map[ 'old_slug' ]
            ) );
            $this->_echo( trim( preg_replace('/\s+/', ' ', $query ) ) );
            $wpdb->query( $query );
            /** Now, remove the actual term taxonomy */
            $query = $wpdb->prepare( "DELETE FROM {$wpdb->term_taxonomy} WHERE taxonomy = %s", array( $map[ 'old_slug' ] ) );
            $this->_echo( $query );
            $wpdb->query( $query );
          }
          /** Ok, first rename the existing taxonomies */
          if( in_array( $map[ 'old_type' ], array( 'enhanced_term', 'term' ) ) && $map[ 'new_type' ] == 'term' ){
            $query = $wpdb->prepare( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = %s WHERE taxonomy = %s", array(
              $map[ 'new_slug' ],
              $map[ 'old_slug' ]
            ) );
            $this->_echo( $query );
            $wpdb->query( $query );
          }
        }
      }

      /**
       * This function converts our enhanced terms
       */
      function convert_enhanced_terms(){
        global $wpdb;
        $this->_echo( 'Converting enhanced terms...' );
        /** Loop through and convert */
        foreach( $this->data_types_map as $type => $map ){
          if( $map[ 'old_type' ] == 'enhanced_term' && $map[ 'new_type' ] == 'post' ){
            if( isset( $this->stage ) && $this->stage && $this->stage != $type ){
              continue;
            }
            /** Get the schema */
            $schema = $this->schemas[ $type ];
            $this->_echo( "Converting all {$type} enhanced terms..." );
            /** Now, get the data for that specific type of term */
            $this->_collect_data( $type );
            /** Make sure we have a new_data array */
            if( !isset( $this->new_data[ $type ] ) || !is_array( $this->new_data[ $type ] ) ){
              $this->new_data[ $type ] = array();
            }
            /** Now, lets loop through each one of these and create posts */
            $posts = array();
            foreach( $this->old_data[ $type ] as $term ){
              if( !$term[ 'term_id' ] ){
                continue;
              }
              /** Ok, now, create the new object */
              $post = $this->_convert_to_object( $term, 'post', $map[ 'map' ] );
              if( isset( $term[ '_post' ] ) ){
                $post = \UsabilityDynamics\Utility::parse_args( $post, $term[ '_post' ] );
              }else{
                $post = \UsabilityDynamics\Utility::parse_args( $post, array(
                  'post_author' => 1,
                  'post_status' => 'publish'
                ) );
              }
              /** Make sure our post type is good */
              $post->post_type = $map[ 'new_slug' ];
              /** Add it to the posts array */
              $posts[ $term[ 'term_id' ] ] = $post;
            }
            /** Make sure the new data is good */
            if( !isset( $this->new_data[ $type ] ) ){
              $this->new_data[ $type ] = array();
            }
            $data =& $this->new_data[ $type ];
            /** Make sure we have a good enhanced posts map */
            if( !isset( $this->cache[ 'enhanced_posts_map' ][ $map[ 'old_slug' ] ] ) ){
              $this->cache[ 'enhanced_posts_map' ][ $map[ 'old_slug' ] ] = array();
            }
            $posts_map =& $this->cache[ 'enhanced_posts_map' ][ $map[ 'old_slug' ] ];
            $posts_map = array();
            /** Now go through each post and add/update it in the DB */
            foreach( $posts as $term_id => $post ){
              /** Update the post */
              $this->_echo( "Updating old term {$term_id}, and ensure there's a post there..." );
              /** Ok, if we have any meta, extract it out */
              if( isset( $post->_meta ) ){
                $meta = $post->_meta;
                unset( $post->_meta );
              }else{
                $meta = array();
              }
              /** Ok, now if we have an ID we can simply update the existing post */
              if( isset( $post->ID ) && is_numeric( $post->ID ) ){
                $post_id = $post->ID;
                wp_update_post( $post );
                $post = get_post( $post_id );
              }else{
                $post_id = wp_insert_post( $post );
                $post = get_post( $post_id );
              }
              /** Enhance the posts map */
              $posts_map[ (int) $term_id ] = (int) $post_id;
              /** Add our 2 meta values */
              $meta[ 'old::extended_term_id' ] = array(
                'post_id' => $post_id,
                'meta_key' => 'old::extended_term_id',
                'meta_value' => $term_id
              );
              $meta[ 'old::extended_term_taxonomy' ] = array(
                'post_id' => $post_id,
                'meta_key' => 'old::extended_term_taxonomy',
                'meta_value' => $map[ 'old_slug' ]
              );
              /***************************************************************************
               * START META
               ***************************************************************************/
              /** Ok, lets go through the meta, updating as we go */
              $_meta = array();
              foreach( $meta as $meta_key => $meta_row ){
                /** Ok, if we're dropping the key we simply delete it */
                if( in_array( $meta_key, $map[ 'meta' ][ 'drop' ] ) ){
                  continue;
                }
                /** Ok, if we have to convert it */
                if( in_array( $meta_key, array_keys( $map[ 'meta' ][ 'rename' ] ) ) ){
                  $meta_row[ 'meta_key' ] = $map[ 'meta' ][ 'rename' ][ $meta_key ];
                  if( @isset( $schema[ 'meta' ][ $map[ 'meta' ][ 'rename' ][ $meta_key ] ] ) && $schema[ 'meta' ][ $map[ 'meta' ][ 'rename' ][ $meta_key ] ][ 'type' ] == 'text' && ( @isset( $schema[ 'meta' ][ $map[ 'meta' ][ 'rename' ][ $meta_key ] ][ 'clone' ] ) && $schema[ 'meta' ][ $map[ 'meta' ][ 'rename' ][ $meta_key ] ][ 'clone' ] === true ) ){
                    if( isset( $_meta[ $map[ 'meta' ][ 'rename' ][ $meta_key ] ] ) ){
                      $temp = unserialize( $_meta[ $map[ 'meta' ][ 'rename' ][ $meta_key ] ][ 'meta_value' ] );
                      $temp[] = $meta_row[ 'meta_value' ];
                      $_meta[ $map[ 'meta' ][ 'rename' ][ $meta_key ] ][ 'meta_value' ] = serialize( $temp );
                    }else{
                      $_meta[ $map[ 'meta' ][ 'rename' ][ $meta_key ] ] = $meta_row;
                      $_meta[ $map[ 'meta' ][ 'rename' ][ $meta_key ] ][ 'meta_value' ] = serialize( array( $_meta[ $map[ 'meta' ][ 'rename' ][ $meta_key ] ][ 'meta_value' ] ) );
                    }
                  }else{
                    $_meta[ $map[ 'meta' ][ 'rename' ][ $meta_key ] ] = $meta_row;
                  }
                  continue;
                }
                /** If we've made it here, no action is required */
                $_meta[ $meta_key ] = $meta_row;
              }
              /** Ok, now we need to continue modify meta, but we do it for each post type differently */
              switch( $map[ 'old_slug' ] ){
                case 'hdp_venue':
                  $_meta[ 'locationGoogleMap' ] = array(
                    'post_id' => $post_id,
                    'meta_key' => 'locationGoogleMap',
                    'meta_value' => ( isset( $meta[ 'latitude' ] ) && isset( $meta[ 'longitude' ] ) ? $meta[ 'latitude' ][ 'meta_value' ] . ',' . $meta[ 'longitude' ][ 'meta_value' ] : '' )
                  );
                  break;
                default:
                  break;
              }
              /** First, delete all the existing meta for the post */
              $this->_echo( "Removing/readding all old meta for post {$post_id}..." );
              $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE post_id = {$post_id}" );
              /** Now loop through the meta and do an insert on each one */
              foreach( $_meta as $meta_key => $meta_row ){
                if( isset( $meta_row[ 'meta_key' ] ) ){
                  $wpdb->insert( $wpdb->postmeta, $meta_row );
                }else{
                  foreach( $meta_row as $meta_row_inner ){
                    $wpdb->insert( $wpdb->postmeta, $meta_row_inner );
                  }
                }
              }
              /** Append the meta back on */
              $post->_meta = $_meta;
              /***************************************************************************
               * END META
               ***************************************************************************/
              /** Ok, now we need to continue modify meta, but we do it for each post type differently */
              switch( $map[ 'old_slug' ] ){
                case 'hdp_venue':
                  /** So we're going to look through the old events, to see if we can find the venue */
                  if( !is_array( $this->old_data[ 'event' ] ) ){
                    $this->old_data[ 'event' ] = array();
                  }
                  foreach( $this->old_data[ 'event' ] as $event_id => $event ){
                    if( @isset( $event[ '_terms' ][ 'hdp_venue' ] ) && is_array( $event[ '_terms' ][ 'hdp_venue' ] ) && count( $event[ '_terms' ][ 'hdp_venue' ] ) ){
                      /** Ok, we have our venues, loop through them to see if it matches */
                      foreach( $event[ '_terms' ][ 'hdp_venue' ] as $venue_id => $venue_term ){
                        if( $meta[ 'extended_term_id' ][ 'meta_value' ] == $venue_id ){
                          /** We found it, let's insert some data */
                          /** Ok, we're going to insert the term relationship for city */
                          foreach( $event[ '_terms' ][ 'hdp_city' ] as $city_id => $city_term ){
                            $data = array(
                              'object_id' => $post_id,
                              'term_taxonomy_id' => $city_term[ 'term_taxonomy_id' ],
                              'term_order' => 0
                            );
                            /** Insert it! */
                            $wpdb->insert( $wpdb->term_relationships, $data );
                          }
                          /** Ok, we're going to insert the term relationship for state */
                          foreach( $event[ '_terms' ][ 'hdp_state' ] as $state_id => $state_term ){
                            $data = array(
                              'object_id' => $post_id,
                              'term_taxonomy_id' => $state_term[ 'term_taxonomy_id' ],
                              'term_order' => 0
                            );
                            /** Insert it! */
                            $wpdb->insert( $wpdb->term_relationships, $data );
                          }
                          /** Now do our country if required */
                          global $ddp_country_term_taxonomy_id;
                          if( !isset( $ddp_country_term_taxonomy_id ) ){
                            /** Insert the term */
                            $wpdb->insert( $wpdb->terms, array(
                              'name' => 'United States',
                              'slug' => 'united-states',
                              'term_group' => 0
                            ) );
                            /** Insert the term taxonomy ID */
                            $wpdb->insert( $wpdb->term_taxonomy, array(
                              'term_id' => $wpdb->insert_id,
                              'taxonomy' => 'country',
                              'description' => '',
                              'parent' => 0,
                              'count' => 0
                            ) );
                            /** Set our term taxonomy id */
                            $ddp_country_term_taxonomy_id = $wpdb->insert_id;
                          }
                          if( $ddp_country_term_taxonomy_id && is_numeric( $ddp_country_term_taxonomy_id ) ){
                            $data = array(
                              'object_id' => $post_id,
                              'term_taxonomy_id' => $ddp_country_term_taxonomy_id,
                              'term_order' => 0
                            );
                            /** Insert it! */
                            $wpdb->insert( $wpdb->term_relationships, $data );
                          }
                        }
                      }
                    }
                  }
                  break;
              }
              /** Ok, add it to the new data */
              $data[ $post_id ] = $post;
            }
          }
        }
      }

      /**
       * This function converts our existing 'post' objects
       */
      function convert_posts(){
        global $wpdb;
        $this->_echo( 'Converting enhanced terms...' );
        /** Loop through and convert */
        foreach( $this->data_types_map as $type => $map ){
          if( $map[ 'old_type' ] == 'post' && $map[ 'new_type' ] == 'post' ){
            if( isset( $this->stage ) && $this->stage && $this->stage != $type ){
              continue;
            }
            /** Get the schema */
            $schema = $this->schemas[ $type ];
            $this->_echo( "Converting all {$type} posts..." );
            /** Now, get the data for that specific type of term */
            $this->_collect_data( $type );
            /** Make sure we have a new_data array */
            if( !isset( $this->new_data[ $type ] ) || !is_array( $this->new_data[ $type ] ) ){
              $this->new_data[ $type ] = array();
            }
            /** Now, lets loop through each one of these and create posts */
            foreach( $this->old_data[ $type ] as $temp_post ){
              /** Convert it to an object */
              $post = new \stdClass();
              foreach( $temp_post as $key => $value ){
                $post->{$key} = $value;
              }
              /** Get the terms and meta  */
              if( isset( $post->_meta ) && is_array( $post->_meta ) && count( $post->_meta ) ){
                $meta = $post->_meta;
              }else{
                $meta = array();
              }
              unset( $post->_meta );
              if( isset( $post->_terms ) && is_array( $post->_terms ) && count( $post->_terms ) ){
                $terms = $post->_terms;
              }else{
                $terms = array();
              }
              unset( $post->_terms );
              /** Get the post ID */
              $post_id = $post->ID;
              $this->_echo( "Converting post to post with ID {$post_id}..." );
              /** Ok, all we need to change on the old post is the slug */
              $post->post_type = $map[ 'new_slug' ];
              /** Ok, lets go through the meta, updating as we go */
              $_meta = array();
              foreach( $meta as $meta_key => $meta_row ){
                /** Ok, if we're dropping the key we simply delete it */
                if( in_array( $meta_key, $map[ 'meta' ][ 'drop' ] ) ){
                  continue;
                }
                /** Ok, if we have to convert it */
                if( in_array( $meta_key, array_keys( $map[ 'meta' ][ 'rename' ] ) ) ){
                  $meta_row[ 'meta_key' ] = $map[ 'meta' ][ 'rename' ][ $meta_key ];
                  $_meta[ $map[ 'meta' ][ 'rename' ][ $meta_key ] ] = $meta_row;
                  continue;
                }
                /** If we've made it here, no action is required */
                $_meta[ $meta_key ] = $meta_row;
              }
              /** Ok, now we need to continue modify meta, but we do it for each post type differently */
              switch( $map[ 'old_slug' ] ){
                case 'hdp_event':
                  $event_date = @isset( $meta[ 'hdp_event_date' ][ 'meta_value' ] ) ? $meta[ 'hdp_event_date' ][ 'meta_value' ] : '01/01/2000';
                  $event_time = @isset( $meta[ 'hdp_event_time' ][ 'meta_value' ] ) ? $meta[ 'hdp_event_time' ][ 'meta_value' ] : '12:00 AM';
                  /** Ok, now we're going to create a date element out of the string */
                  $this->_echo( 'Converting the following date: ' . $event_date . ' ' . $event_time . '...' );
                  $dateStart = date_create_from_format( 'm/d/Y h:i A', $event_date . ' ' . $event_time );
                  $dateEnd = date_create_from_format( 'm/d/Y h:i A', $event_date . ' 03:00 AM' )->modify( '+1 day' );
                  /** Set the new meta */
                  $_meta[ 'dateStart' ] = array(
                    'post_id' => $post_id,
                    'meta_key' => 'dateStart',
                    'meta_value' => $dateStart->format( 'Y-m-d H:i' )
                  );
                  $_meta[ 'dateEnd' ] = array(
                    'post_id' => $post_id,
                    'meta_key' => 'dateEnd',
                    'meta_value' => $dateEnd->format( 'Y-m-d H:i' )
                  );
                  break;
                default:
                  break;
              }
              $taxonomy_map = array();
              $taxonomy_to_meta_map = array();
              /** Ok, go through and build our term map */
              foreach( $this->data_types_map as $temp_type => $temp_map ){
                /** If our old type was an enhanced_term, then we have to convert */
                if  ( $temp_map[ 'old_type' ] == 'enhanced_term' && $temp_map[ 'new_type' ] == 'post' ){
                  $taxonomy_to_meta_map[ $temp_map[ 'old_slug' ] ] = $temp_map[ 'meta_name' ];
                }elseif( $temp_map[ 'new_type' ] == 'term' ){
                  $taxonomy_map[ $temp_map[ 'old_slug' ] ] = $temp_map[ 'new_slug' ];
                }
              }
              /** Now, we have our meta - we should go ahead and convert the terms */
              $_terms = array();
              foreach( $terms as $taxonomy => $taxonomy_terms ){
                /** Ok, first if we need to change ourselves to a meta object */
                if( in_array( $taxonomy, array_keys( $taxonomy_to_meta_map ) ) ){
                  /** Pull in the cache */
                  $enhanced_posts_map = array();
                  if( @isset( $this->cache[ 'enhanced_posts_map' ][ $taxonomy ] ) ){
                    $enhanced_posts_map = $this->cache[ 'enhanced_posts_map' ][ $taxonomy ];
                  }
                  /** Now, lets loop through the terms themselves */
                  foreach( $taxonomy_terms as $term_id => $term ){
                    /** Ok, so we're just going to add it to the meta if it is found in our map */
                    if( @isset( $enhanced_posts_map[ (int) $term_id ] ) ){
                      $new_meta_value = array(
                        'post_id' => $post_id,
                        'meta_key' => $taxonomy_to_meta_map[ $taxonomy ],
                        'meta_value' => $enhanced_posts_map[ (int) $term_id ]
                      );
                      if( isset( $_meta[ $taxonomy_to_meta_map[ $taxonomy ] ] ) && isset( $_meta[ $taxonomy_to_meta_map[ $taxonomy ] ][ 'meta_key' ] ) ){
                        $_meta[ $taxonomy_to_meta_map[ $taxonomy ] ] = array( $_meta[ $taxonomy_to_meta_map[ $taxonomy ] ] );
                      }
                      if( isset( $_meta[ $taxonomy_to_meta_map[ $taxonomy ] ] ) ){
                        $_meta[ $taxonomy_to_meta_map[ $taxonomy ] ][] = $new_meta_value;
                      }else{
                        $_meta[ $taxonomy_to_meta_map[ $term[ 'taxonomy' ] ] ] = $new_meta_value;
                      }
                    }
                  }
                }
              }
              /** Go ahead and save the post */
              wp_update_post( $post );
              /** First, delete all the existing meta for the post */
              $this->_echo( "Removing/readding all old meta for post {$post_id}..." );
              $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE post_id = {$post_id}" );
              /** Now loop through the meta and do an insert on each one */
              foreach( $_meta as $meta_key => $meta_row ){
                if( isset( $meta_row[ 'meta_key' ] ) ){
                  $wpdb->insert( $wpdb->postmeta, $meta_row );
                }else{
                  foreach( $meta_row as $meta_row_inner ){
                    $wpdb->insert( $wpdb->postmeta, $meta_row_inner );
                  }
                }
              }
              $post->_meta = $_meta;
              $this->new_data[ $type ][ $post_id ] = $post;
            }
          }
        }
      }

      /**
       * Common function to convert using a map from one form to another
       */
      function _convert_to_object( $from, $to, $map ){
        $data = array();
        foreach( $map as $source => $destination ){
          unset( $value );
          if( stripos( $source, '.' ) === false ){
            /** Basic attempt */
            if( isset( $from[ $source ] ) ){
              $value = $from[ $source ];
            }
          }else{
            /** We have to explode the item to traverse the array */
            $parts = explode( '.', $source );
            $check = null;
            foreach( $parts as $count => $part ){
              if( $count == count( $parts ) - 1 ){
                continue;
              }
              if( is_null( $check ) ){
                /** Check the from object */
                if( !isset( $from[ $part ] ) ){
                  continue;
                }
                /** Assign our first object */
                $check = $from[ $part ];
                continue;
              }else{
                /** We're on subsequent loops, we check, check */
                if( !isset( $check[ $part ] ) ){
                  continue;
                }
                /** Update and loop */
                $check = $check[ $part ];
                continue;
              }
            }
            if( isset( $check[ $part ] ) ){
              $value = $check[ $part ];
            }
          }

          /** We have a value, see if we have a place to put it */
          if( isset( $value ) && !is_null( $value ) ){
            /** Ok, find where it goes on the destination */
            if( stripos( $destination, '.' ) === false ){
              /** Basic attempt */
              if( !isset( $data[ $destination ] ) ){
                $data[ $destination ] = $value;
                continue;
              }
              /** If it's not an array */
              if( !is_array( $data[ $destination ] ) ){
                die( "migrate.php:450ish:This hasn't come up yet... @todo" );
              }
              /** We know it's an array, add onto it */
              die( "migrate.php:450ish:This hasn't come up yet... @todo" );

            }else{
              /** We have to explode the item to traverse the array */
              $parts = explode( '.', $destination );
              if( isset( $last ) ){
                unset( $last );
              }
              foreach( $parts as $count => $part ){
                if( $count == count( $parts ) - 1 ){
                  continue;
                }
                if( is_null( $last ) ){
                  /** First go through */
                  if( !isset( $data[ $part ] ) ){
                    $data[ $part ] = array();
                  }
                  /** Update $last */
                  $last =& $data[ $part ];
                  /** Continue */
                  continue;
                }
                /** Otherwise, make sure we have something set with last */
                if( !isset( $last[ $part ] ) ){
                  $last[ $part ] = array();
                }
                /** Update $last */
                $last =& $last[ $part ];
                /** Continue */
                continue;
              }

              /** If it's the first time we're setting it */
              if( !isset( $last[ $part ] ) ){
                $last[ $part ] = $value;
                continue;
              }

              /** If it's there, but it's not an array */
              if( !is_array( $last[ $part ] ) ){
                $last[ $part ] = array( $last[ $part ] );
                $last[ $part ][] = $value;
                continue;
              }

              /** It's already an array */
              $last[ $part ][] = $value;
              continue;
            }
          }
        }
        /** Ok, now we have a data object - we need to do different things based on the new post type */
        if( !count( $data ) ){
          $this->_echo( "Could not convert object : " . print_r( $from, true ) . "..." );
          return false;
        }
        /** Go ahead and return the data */
        return $data;
      }

      /**
       * This function is going to kick off our migration by getting all the old data that needs to be converted in one
       * go - in addition it will call the following, first checking for the top, and then the bottom:
       *  - $this->_collect_data_{slug} (i.e. _collect_data_artist)
       *  - $this->_collect_data_{type} (i.e. _collect_data_enhanced_post)
       *
       * All functions should have the following arguments
       *  - $map (array) The mapped object
       *  - &$data (array) The data object you're adding your items onto
       */
      function _collect_data( $type = false ){
        /** Ok, we're first going to create our objects in the data array */
        foreach( $this->data_types_map as $key => $value ){
          /** If we have a specific stage, do it */
          if( $this->stage && $key != $this->stage ){
            continue;
          }
          if( $type && $key != $type ){
            continue;
          }
          $this->old_data[ $key ] = array();
        }
        /** Now, we're going to look for our function */
        foreach( $this->data_types_map as $key => $value ){
          if( $this->stage && $key != $this->stage ){
            continue;
          }
          $this->_echo( "=" );
          $this->_echo( "Attempting to collect the data for: " . $key );
          $this->_echo( "-" );
          $this->_echo( $value );
          $this->_echo( "-" );
          if( is_callable( array( $this, '_collect_data_' . $key ) ) ){
            $this->_echo( "Calling function: '_collect_data_{$key}'..." );
            call_user_func_array( array( $this, '_collect_data_' . $key ), array(
              $value,
              &$this->old_data[ $key ]
            ) );
          }elseif( is_callable( array( $this, '_collect_data_' . $value[ 'old_type' ] ) ) ){
            $this->_echo( "Calling function: '_collect_data_{$value[ 'old_type' ]}'..." );
            call_user_func_array( array( $this, '_collect_data_' . $value[ 'old_type' ] ), array(
              $value,
              &$this->old_data[ $key ]
            ) );
          }
        }
      }

      /**
       * Ok, this is our first data collector, we're going to get
       * all enhanced terms
       */
      function _collect_data_enhanced_term( $map, &$data ){
        global $wpdb;
        /** First, run it through the normal term function */
        $this->_collect_data_term( $map, $data );
        /** Now go through and get the enhanced data */
        if( !isset( $this->cache[ 'enhanced_posts_map' ] ) ){
          $this->cache[ 'enhanced_posts_map' ] = array();
          $query = "
            SELECT DISTINCT
              post_id,
              meta_value,
              post_type
            FROM
              {$wpdb->postmeta} AS pm LEFT JOIN
              {$wpdb->posts} AS p ON pm.post_ID = p.ID
            WHERE
              meta_key = 'extended_term_id'
          ";
          $results = $wpdb->get_results( $query, ARRAY_A );
          /** Ok, we have some results, lets loop through and build our cache */
          foreach( $results as $row ){
            $type = str_ireplace( '_tp_', '', $row[ 'post_type' ] );
            /** No type? Bail */
            if( !$type ){
              continue;
            }
            /** Ensure we have it set by type */
            if( !isset( $this->cache[ 'enhanced_posts_map' ][ $type ] ) ){
              $this->cache[ 'enhanced_posts_map' ][ $type ] = array();
            }
            /** Now add on to it */
            if( $row[ 'meta_value' ] ){
              $this->cache[ 'enhanced_posts_map' ][ $type ][ (int) $row[ 'meta_value' ] ] = (int) $row[ 'post_id' ];
            }
          }
        }
        /** Ok, now bring in the posts map */
        $posts_map = @$this->cache[ 'enhanced_posts_map' ][ $map[ 'old_slug' ] ];
        /** Get all my posts */
        $post_ids = array();
        /** Now, get all the posts */
        foreach( $data as $term_id => &$term ){
          if( !$term_id ){
            continue;
          }
          if( isset( $posts_map[ $term_id ] ) ){
            $post_ids[] = $posts_map[ $term_id ];
          }
        }
        /** Now, get all of our posts */
        $query ="
          SELECT
            *
          FROM
            {$wpdb->posts}
          WHERE
            ID IN ( " . implode( ', ', $post_ids ) . " )
        ";
        $results = $wpdb->get_results( $query, ARRAY_A );
        $posts = array();
        foreach( $results as $row ){
          $posts[ $row[ 'ID' ] ] = $row;
        }
        /** Add on the meta */
        $this->posts_add_meta( $posts );
        /** Add on the terms */
        $this->posts_add_terms( $posts );

        /** Ok, we finally made it, add it to our object */
        $post_map_flipped = array_flip( $posts_map );
        foreach( $posts as $post ){
          if( !isset( $post_map_flipped[ $post[ 'ID' ] ] ) ){
            continue;
          }
          $data[ $post_map_flipped[ $post[ 'ID' ] ] ][ '_post' ] = $post;
        }
      }

      /**
       * This function gets all regular terms
       */
      function _collect_data_term( $map, &$data ){
        global $wpdb;
        /** Ok, get all the terms from term_tax and terms */
        $query = $wpdb->prepare( "
          SELECT
            *
          FROM
            {$wpdb->term_taxonomy} AS tt LEFT JOIN
            {$wpdb->terms} AS t ON tt.term_id = t.term_id
          WHERE
            taxonomy = %s
        ", array(
          $map[ 'old_slug' ]
        ) );
        $results = $wpdb->get_results( $query, ARRAY_A );
        /** Ok, loop through the array and assign it to where the 'key' is the term ID */
        foreach( $results as $term ){
          $data[ $term[ 'term_id' ] ] = $term;
        }
      }

      /**
       * This function gets all data on posts
       */
      function _collect_data_post( $map, &$data ){
        global $wpdb;
        /** Get all the posts directly from the DB */
        $query = $wpdb->prepare( "
          SELECT
            *
          FROM
            {$wpdb->posts}
          WHERE
            post_type = %s
            AND
            post_status = 'publish'
        ", array(
          $map[ 'old_slug' ]
        ) );
        $results = $wpdb->get_results( $query, ARRAY_A );
        /** Loop through and assign it to the data array */
        foreach( $results as $row ){
          $data[ $row[ 'ID' ] ] = $row;
        }
        /** Add on our meta */
        $this->posts_add_meta( $data );
        /** Add on our terms */
        $this->posts_add_terms( $data );
      }

      /**
       * This function adds terms to an array of post objects
       *
       * @param @$array Array of post to add terms to
       */
      function posts_add_terms( &$posts ){
        global $wpdb;
        /** Get our terms */
        $query = "
          SELECT
            *
          FROM
            {$wpdb->term_relationships} AS tr LEFT JOIN
            {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id LEFT JOIN
            {$wpdb->terms} AS t ON tt.term_id = t.term_id
          WHERE
            tr.object_id IN ( " . implode( ', ', array_keys( $posts ) )  . " )
        ";
        $results = $wpdb->get_results( $query, ARRAY_A );
        if( !$results || !count( $results ) ){
          return;
        }
        /** Loop through the results and add it to the post object */
        foreach( $results as $row ){
          if( !isset( $posts[ $row[ 'object_id' ] ] ) ){
            continue;
          }
          if( !isset( $posts[ $row[ 'object_id' ] ][ '_terms' ] ) ){
            $posts[ $row[ 'object_id' ] ][ '_terms' ] = array();
          }
          if( !isset( $posts[ $row[ 'object_id' ] ][ '_terms' ][ $row[ 'taxonomy' ] ] ) ){
            $posts[ $row[ 'object_id' ] ][ '_terms' ][ $row[ 'taxonomy' ] ] = array();
          }
          $posts[ $row[ 'object_id' ] ][ '_terms' ][ $row[ 'taxonomy' ] ][ $row[ 'term_id' ] ] = $row;
        }
      }

      /**
       * This function adds meta onto an array of post object
       *
       * @param @$array Array of posts to add meta to
       */
      function posts_add_meta( &$posts ){
        global $wpdb;
        if( !is_array( $posts ) || ( is_array( $posts ) && !count( $posts ) ) ){
          return;
        }
        /** Now, get our post meta */
        $query = "
          SELECT
            *
          FROM
            {$wpdb->postmeta}
          WHERE
            post_id IN ( " . implode( ', ', array_keys( $posts ) ) . " )
        ";
        $results = $wpdb->get_results( $query, ARRAY_A );
        foreach( $results as $row ){
          if( !isset( $posts[ $row[ 'post_id' ] ] ) ){
            continue;
          }
          $post =& $posts[ $row[ 'post_id' ] ];
          if( !isset( $post[ '_meta' ] ) ){
            $post[ '_meta' ] = array();
          }
          /** Ok, here is where we check for duplicate post metas */
          if( isset( $post[ '_meta' ][ $row[ 'meta_key' ] ] ) && isset( $post[ '_meta' ][ $row[ 'meta_key' ] ][ 'meta_id' ] ) ){
            $post[ '_meta' ][ $row[ 'meta_key' ] ] = array( $post[ '_meta' ][ $row[ 'meta_key' ] ] );
          }
          /** Add on to the meta now */
          if( @is_array( $post[ '_meta' ][ $row[ 'meta_key' ] ] ) && !isset( $post[ '_meta' ][ $row[ 'meta_key' ] ][ 'meta_id' ] ) ){
            $post[ '_meta' ][ $row[ 'meta_key' ] ][] = $row;
          }else{
            $post[ '_meta' ][ $row[ 'meta_key' ] ] = $row;
          }
        }
      }

      /**
       * This function gets the post type meta keys found in the DB for
       * a particular post type
       */
      function get_post_type_meta( $post_type ){
        global $wpdb;
        $sql = "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} AS pm LEFT JOIN {$wpdb->posts} AS p ON pm.post_ID = p.ID WHERE post_type LIKE %s ORDER BY meta_key ASC";
        $query = $wpdb->prepare( $sql, array( $post_type ) );
        return $wpdb->get_col( $query );
      }

      /**
       * Maybe echo a log out
       */
      function _echo( $msg ){
        if( $msg == '-' ){
          $msg = "--------------------------------------------";
        }
        if( $msg == '=' ){
          $msg = "============================================";
        }
        if( defined( 'WP_CLI' ) && WP_CLI ){
          if( !is_string( $msg ) ){
            \WP_CLI::print_value( $msg );
          }else{
            \WP_CLI::line( $msg );
          }
        }else{
          if( $this->dump_logs ){
            if( !isset( $this->headers_sent ) ){
              header( "Content-Type: text/plain" );
              $this->headers_sent = true;
            }
            echo $msg . "\r\n";
          }else{
            $this->logs[] = $msg;
          }
        }
      }

    }
  }

}
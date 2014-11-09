<?php
namespace UsabilityDynamics\wpElastic {

  if( !class_exists( 'UsabilityDynamics\wpElastic\Document' ) ) {

    class Document {

      static $_type = 'post';

      function __construct() {

      }

      static function normalize( $post ) {

        $doc = array();

        $doc[ 'event_date_time' ]         = date( 'c', strtotime( get_post_meta( $post->ID, 'hdp_event_date', 1 ) . ' ' . get_post_meta( $post->ID, 'hdp_event_time', 1 ) ) );
        $doc[ 'event_date_human_format' ] = date( 'F j, Y', strtotime( get_post_meta( $post->ID, 'hdp_event_date', 1 ) . ' ' . get_post_meta( $post->ID, 'hdp_event_time', 1 ) ) );
        $lat                              = get_post_meta( $post->ID, 'latitude', 1 );
        $lon                              = get_post_meta( $post->ID, 'longitude', 1 );
        $doc[ 'location' ]                = array(
          'lat' => (float) ( !empty( $lat ) ? $lat : 0 ),
          'lon' => (float) ( !empty( $lon ) ? $lon : 0 )
        );
        $doc[ 'raw' ]                     = get_event( $post->ID );
        $doc[ 'permalink' ]               = get_permalink( $post->ID );
        $doc[ 'image_url' ]               = flawless_image_link( $doc[ 'raw' ][ 'event_poster_id' ], 'events_flyer_thumb' );

      }

      /**
       * Initializes Bridge by Adding Filters
       *
       * @since 1.0.0
       * @author potanin@UD
       */
      static function initialize( $args = array() ) {

        if( $args[ 'types' ] ) {
          update_option( self::$option . '::types', $args[ 'types' ] );
        }

        add_filter( 'wp_trash_post', array( __CLASS__, 'delete' ), 100 );
        add_filter( 'save_post', array( __CLASS__, 'index' ), 100 );
        add_action( 'post_submitbox_misc_actions', array( __CLASS__, 'post_submitbox_misc_actions' ) );
      }

      /**
       * Synchronized listings with Cloud Searching Service
       *
       * @todo Add extend() to args. - potanin@UD 10/08/12
       * @version 1.1
       * @since 2.0
       */
      static function index( $post = false ) {
        $types = (array) get_option( self::$option . '::types' );
        $_post = $post;

        if( !$post ) {
          return new WP_Error( __METHOD__, 'Unable to index, no documents passed.' );
        }

        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
          return $_post;
        }

        if( wp_is_post_revision( $post ) ) {
          return null;
        }

        if( is_numeric( $post ) ) {
          $post = get_post( $post );
        }

        if( is_object( $post ) && $post->post_type && !in_array( $post->post_type, $types ) ) {
          return $_post;
        }

        if( !get_option( 'ud::cloud::access-hash', defined( 'UD_Cloud_Access_Hash' ) ? UD_Cloud_Access_Hash : null ) ) {
          return new WP_Error( __METHOD__, 'Unable to index, cloud Access Hash not set.' );
        }

        self::timer_start( __METHOD__ );

        //** Why json_decode - json_encode? -> Deep Conversion to Object */
        $post = json_decode( json_encode( $post ) );

        foreach( (array) self::strip_protected_keys( (array) get_metadata( 'post', $post->ID ) ) as $meta_key => $values ) {
          $post->{$meta_key} = array_shift( array_values( $values ) );
        }

        $post->terms    = is_object( $post ) && $post->terms ? $post->terms : wp_get_object_terms( $post->ID, get_object_taxonomies( $post->post_type ) );
        $post->comments = is_object( $post ) && $post->comments ? $post->comments : get_comments( array( 'post_id' => $post->ID ) );

        // Execute Filters and Clean
        $post = self::array_filter_deep( apply_filters( 'ud::cloud::document', $post ) );

        if( !$post ) {
          return null;
        }

        $post = json_decode( json_encode( $post ) );

        // Private API call can use Customer Key
        $post_url = implode( '/', array(
          self::$url,
          'api/v' . self::$api_version,
          'documents',
          $post->type ? $post->type : 'post',
          $post->id
        ) );

        $_response = wp_remote_post( $post_url, array(
          'sslverify' => false,
          'timeout'   => 60,
          'body'      => array(
            'document' => $post
          ),
          'headers'   => array(
            'Authorization' => 'Basic ' . base64_encode( get_option( 'ud::cloud::access-hash' ) )
          )
        ) );

        if( is_wp_error( $_response ) ) {
          return $_response;
        }

        $_response[ 'post_url' ] = $post_url;
        $_response[ 'timer' ]    = self::timer_stop( __METHOD__ );

        $body = $_response[ 'body' ] = (object) array_filter( (array) json_decode( $_response[ 'body' ], true ) );

        update_post_meta( $post->id, 'ud::cloud::id', $body->id );
        update_post_meta( $post->id, 'ud::cloud::version', $body->meta->version );
        update_post_meta( $post->id, 'ud::cloud::synchronized', time() );

        /*

        $item = $_response[ 'body' ];

        switch( true ) {

          case $_response[ 'response' ][ 'code' ] == 500:
            self::object_log( $post->ID, new WP_Error( __METHOD__, 'UD Cloud API error occured during an attempt to index.', $_response ) );
            $validated = false;
          break;

          case ( is_object( $item ) && !$item->success && $item->message ):
            self::object_log( $post->ID, new WP_Error( __METHOD__, $item->message, $_response ) );
            $validated = false;
          break;

          case ( is_object( $item ) && !$item->success ):
            self::object_log( $post->ID, new WP_Error( __METHOD__, 'An error occurred with the following document: ' . $post->ID, $_response ) );
            $validated = false;
          break;

          case ( is_object( $item ) && $item->error ):
            self::object_log( $post->ID, new WP_Error( __METHOD__, 'An error occurred with the following document: ' . $post->ID . ' with message: ' . $item->error, $_response ) );
            $validated = false;
          break;

          case ( is_object( $item ) && !$item->id ):
            self::object_log( $post->ID, new WP_Error( __METHOD__, 'The document could not be properly indexed: ' . $post->ID, $_response ) );
            $validated = false;
          break;

          case ( is_object( $item ) && $post->ID != $item->id ):
            self::object_log( $post->ID, new WP_Error( __METHOD__, 'Item ID mismatch detected:' . $post->ID . ' and ' . $item->id, $_response ) );
            $validated = false;
          break;

        }

        return 'Cloud Update process complete in ' . self::timer_stop( __METHOD__ ) . ' seconds.' : new WP_Error( 'UD_Cloud::index', 'Unknown error occured during indexing.', $_response );

       */

        return $body;

      }

      /**
       * Delete Document from Cloud
       *
       * @action before_delete_post|wp_trash_post
       * @todo Must use DELETE method for request, also uses wrong key.
       */
      static function delete( $id ) {

        if( is_numeric( $id ) ) {
          $post = get_post( $id );
        }

        if( !$post ) {
          return;
        }

        if( !in_array( $post->post_type, (array) get_option( self::$option . '::types' ) ) ) {
          return;
        };

        if( !get_post_meta( $post->ID, 'ud::cloud::id', true ) ) {
          return;
        }

        // Private API call can use Customer Key
        $url = implode( '/', array( self::$url, 'api/v' . self::$api_version, 'documents', $post->post_type, get_post_meta( $post->ID, 'ud::cloud::id', true ) ) );

        wp_remote_request( $url, array( 'method' => 'DELETE' ) );

        delete_post_meta( $id, 'ud::cloud::id' );
        delete_post_meta( $id, 'ud::cloud::version' );
        delete_post_meta( $id, 'ud::cloud::synchronized' );

      }

      /**
       * Updates system log.
       *
       * @since 1.0.0
       * @author potanin@UD
       */
      static function log( $data ) {

        if( defined( 'WP_DEBUG' ) && WP_DEBUG && is_wp_error( $data ) ) {
          wp_die( '<h1>Debug Log</h1><pre>' . print_r( $data, true ) . '</pre>' );
        }

        return $data;

      }

      /**
       * Updates object log
       *
       * @since 1.0.1
       */
      static function object_log( $id, $data ) {
        add_post_meta( $id, 'ud::cloud::log', $data );

        return $id;
      }

      /**
       * Initializes Bridge by Adding Filters
       *
       * @since 1.0.0
       * @author potanin@UD
       */
      static function post_submitbox_misc_actions() {

        global $post;

        if( $synchronized = get_post_meta( $post->ID, 'ud::cloud::synchronized', true ) ) {
          $synchronized = human_time_diff( $synchronized ) . ' ago';

          if( $synchronized == '1 min ago' ) {
            $synchronized = 'Just now';
          }

        }

        if( !$synchronized && !in_array( $post->post_type, (array) get_option( self::$option . '::types' ) ) ) {
          return;
        }

        $html = array();

        $html[ ] = '<div class="misc-pub-section curtime">';

        if( $synchronized ) {
          $html[ ] = '<span id="timestamp">Cloud Synchronization: <b>' . $synchronized . '</b></span>';
        } else {
          $html[ ] = '<span id="timestamp">Pending Cloud Synchronization.</span>';
        }

        $html[ ] = '</div>';

        echo implode( '', $html );

      }

    }

  }

}
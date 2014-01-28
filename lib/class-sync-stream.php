<?php
/**
 * Sync
 *
 * @version 0.1.0
 * @author Usability Dynamics
 * @namespace UsabilityDynamics
 */

namespace UsabilityDynamics\Festival {

  if( !class_exists( 'UsabilityDynamics\Festival\Sync_Stream' ) ) {

    /**
     * Abstract class for Social Network Syncronization
     */
    abstract class Sync_Stream {

      /**
       * Request Results
       */
      protected $results = array();
    
      /**
       * 
       * @var type
       */
      public $mapping = array(
        'post_title' => '',
        'attachments' => '',
        '_ss_network' => '',
        '_ss_id' => '', // Required. Unique ID.
        '_ss_content' => '',
        '_ss_created_at' => '',
        '_ss_author' => '',
        '_ss_url' => '',
      );
      
      /**
       *
       */
      public $id = null;

      /**
       * 
       * @var string
       */
      public $post_type = 'post';
      
      /**
       * 
       */
      public $interval = '86400';
      
      /**
       * Oauth credentials. Can be different, - depend on network.
       *
       * @var array. 
       */
      public $oauth = array();
      
      /**
       *
       */
      private $errors = array();      

      /**
       * Construct
       * @param type $options:
       * - oauth. Array. Required. Oauth credentials. Can be different, - depend on network.
       * - id. String. Optional. Unique ID for instance.
       * - post_type. String. Optional. Default is 'post'
       * - interval. Numeric. Optional. If false, sync called immediately.
       */
      public function __construct( $options = array() ) {

        // STEP 1. Set arguments.
      
        $options = wp_parse_args( $options );
        
        $this->id = !empty( $options[ 'id' ] ) ? $options[ 'id' ] : sanitize_key( get_class( $this ) );
        $this->post_type = !empty( $options[ 'post_type' ] ) ? $options[ 'post_type' ] : $this->post_type;
        $this->oauth = !empty( $options[ 'oauth' ] ) && is_array( $options[ 'oauth' ] ) ? $options[ 'oauth' ] : array();
        $this->request = !empty( $options[ 'request' ] ) && is_array( $options[ 'request' ] ) ? $options[ 'request' ] : array();
        
        $this->mapping = wp_parse_args( ( !empty( $options[ 'mapping' ] ) && is_array( $options[ 'mapping' ] ) ? $options[ 'mapping' ] : array() ), $this->mapping );
        
        // Determine if interval is set in options. If it's not numeric, we think that it's 'false' and do sync call immediately.
        if( isset( $options[ 'interval' ] ) ) {
          $this->interval = is_numeric( $options[ 'interval' ] ) ? $options[ 'interval' ] : false;
        }
        
        // Try to call sync
        $this->maybe_sync();
        
      }

      /**
       * Should be extended with specific connection (fb, tw, ig).
       * Should return some handy object to work with using methods like get_last_items() etc..
       * Should not be called directly.
       */
      abstract public function do_request();
      
      /**
       * Get errors.
       * If there are no errors. Boolean 'false' will be returned.
       *
       * @return mixed
       */
      public function get_errors() {
        return !empty( $this->errors ) ? (array) $this->errors : false;
      }
      
      /**
       * Set errors
       */
      public function set_errors( $message ) {
        if( !is_array( $this->errors ) ) {
          $this->errors = array();
        }
        $this->errors[] = $message;
      }
      
      /**
       * Does synchronization
       */
      private function sync() {
        global $wpdb;
        
        try {
          
          if( empty( $this->mapping ) ) {
            throw new \Exception( 'Mapping must be set' );
          }
          
          if( empty( $this->oauth ) ) {
            throw new \Exception( 'Credentials must be set' );
          }
          
          $this->do_request();
          
          if( empty( $this->results ) || !is_array( $this->results ) ) {
            throw new \Exception( 'Could not get results' );
          }
          
          foreach( $this->results as $item ) {

            $data = $this->_parse_item( $item );
            
            // Ignore items without unique ID
            if( empty( $data[ '_ss_id' ] ) ) {
              throw new \Exception( 'Unique ID is not set. Check property $mapping.' );
            }
            
            $data[ '_ss_unique_id' ] = $this->id . '::' . $data[ '_ss_id' ];
            
            // Be sure that we don't have the current data.
            $res = $wpdb->get_col( "
              SELECT post_id
                FROM {$wpdb->postmeta} 
              WHERE meta_key = '_ss_unique_id' 
                AND meta_value = '{$data[ '_ss_unique_id' ]}'
            " );
            
            // Ignore if post already exists.
            if( !empty( $res ) ) {
              continue;
            }
            
            $post_id = wp_insert_post( array(
              'post_title' => $data[ 'post_title' ],
              'post_type' => $this->post_type,
              'post_status' => 'publish',
            ), true );
            
            if( is_wp_error( $post_id ) ) {
              throw new \Exception( $post_id->get_error_message() );
            }
            
            foreach( $data as $k => $v ) {
              if( !in_array( $k, array( 'post_title', 'attachments' ) ) ) {
                add_post_meta( $post_id, $k, $v );
              }
            }
            
            // Upload and attach media
            if( !empty( $data[ 'attachments' ] ) ) {
            
              $upload_dir = wp_upload_dir();
            
              $r = \UsabilityDynamics\Utility::image_fetch( $data[ 'attachments' ], array(
                'upload_dir' => $upload_dir[ 'path' ],
                'timeout' => 10,
              ) );
              
              if( !is_object( $r ) || empty( $r->images ) || !is_array( $r->images )  ) {
                throw new \Exception( 'Error on image uploading.' );
              }
              
              foreach( $r->images as $image ) {
                $hash = md5( $image->source_url );
                $attachment = array(
                  'post_mime_type' => $image->type,
                  'post_name' => $hash,
                  'post_parent' => $post_id,
                  'post_title' => $hash,
                );

                $thumb_id = wp_insert_attachment( $attachment, $image->file, $post_id );

                if ( !is_wp_error( $thumb_id ) ) {
                  // first include the image.php file
                  // for the function wp_generate_attachment_metadata() to work
                  require_once( ABSPATH . 'wp-admin/includes/image.php' );
                  $attach_data = wp_generate_attachment_metadata( $thumb_id, $image->file );
                  wp_update_attachment_metadata( $thumb_id, $attach_data );
                  
                  update_post_meta( $thumb_id, 'wpp_imported_image', true );
                }
              }
              
            }
            
          }
          
        } catch ( \Exception $e ) {
          $this->set_errors( $e->getMessage() );
          //echo "<pre>"; print_r( $this->get_errors() ); echo "</pre>"; die();
          return false;
          
        }
        
        update_option( "sync_stream::{$this->id}::last", time() );
        
        return true;
      }
      
      /**
       * 
       */
      private function maybe_sync() {
        if( $this->interval && $this->interval > 0 ) {
          $last_sync_time = get_option( "sync_stream::{$this->id}::last" );
          // Determine if sync should be called now.
          if( !$last_sync_time || ( $last_sync_time + $this->interval ) > time() ) {
            return false;
          }
        }
        return $this->sync();
      }

      /**
       *
       */
      private function _parse_item( $item ) {
        $result = array();
        
        foreach( $this->mapping as $key => $map ) {
        
          if( is_string( $map ) ) {
            $map = array( $map, 'string' );
          }
          
          $_value = $map[0];
          
          $is_mutiple = in_array( $map[1], array( 'media' ) ) ? true : false;
          
          preg_match_all( '/\[(.*?)\]/', $map[0], $matches );
          
          if( !empty( $matches ) ) {
            foreach( $matches[1] as $k => $match ) {
              
              $value = $this->_get( $match, $item );
              
              if( $is_mutiple ) {
                // get rid from string
                $value = !empty( $value ) ? $value : array();
                if( !is_array( $value ) ) {
                  $value = array( $value );
                }
              } else {
                // get rid from array.
                if( is_array( $value ) ) {
                  $value = array_shift( $value );
                  $value = is_string( $value ) ? $value : '';
                }
                $value = str_replace( $matches[0][ $k ], $value, $_value );
              }
              $_value = $value;
            }
          }
          
          // Go through different conditions
          switch( $map[1] ) {
              
            case 'date':
              $_value = strtotime( $_value );
              break;
              
          }
          
          $result[ $key ] = $_value;
          
        }
        
        return $result;
      }
      
      /**
       * Parses array for getting multiple values using hard-structure keys.
       * Returns multiple or single values
       *
       * Example:
       *
       * 1. key:
       * one_key.two_key:third_keys
       *
       * 2. array for search the values:
       * array(
       *  'one_key' => array(
       *    'two_key' => array(
       *      '0' => array(
       *        'third_keys' => 'value1'
       *      ),
       *      '1' => array(
       *        'third_keys' => 'value2'
       *      ),
       *      '2' => array(
       *        'third_keys' => 'value3'
       *      )
       *    )
       *  ) 
       * )
       *
       * 3. will return:
       * array( 'value1', 'value2', 'value3' )
       *       
       * @param string $crumbs Key
       * @param array $item Array where we try to get values from
       * @author peshkov@UD
       */
      private function _get( $key, $item ) {
        $crumbs = explode( ':', $key );
        $crumb = array_shift( $crumbs );
        $data = $this->_get_by_key( $crumb, $item );
        if( !empty( $crumbs ) ) {
          $_data = array();
          if( is_array( $data ) ) {
            $key = implode( ':', $crumbs );
            foreach( $data as $k => $v ) {
              $_data[] = $this->_get( $key, $v );
            }
          }
          $data = $_data;
        }
        return $data;
      }
      
      /**
       * Returns value by passed key from passed array 
       *
       * @param bool $default
       * @return type
       */
      private function _get_by_key( $key, $arr, $default = '' ) {
        // Resolve dot-notated key.
        if( strpos( $key, '.' ) ) {
          return $this->_resolve( $arr, $key, $default );
        }
        // Return value or default.
        return isset( $arr[ $key ] ) ? $arr[ $key ] : $default;
      }
      
      /**
       * Resolve dot-notated key.
       *
       * @source http://stackoverflow.com/questions/14704984/best-way-for-dot-notation-access-to-multidimensional-array-in-php
       * @param       $a
       * @param       $path
       * @param null  $default
       * @internal param array $a
       * @return array|null
       */
      private function _resolve( $a, $path, $default = null ) {
        $current = $a;
        $p = strtok( $path, '.' );
        while( $p !== false ) {
          if( !isset( $current[ $p ] ) ) {
            return $default;
          }
          $current = $current[ $p ];
          $p = strtok( '.' );
        }
        return $current;
      }
      
    }

  }

}
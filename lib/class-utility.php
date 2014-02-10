<?php
/**
 * Festival Utility
 *
 * @version 0.1.0
 * @author Usability Dynamics
 * @namespace UsabilityDynamics
 */
namespace UsabilityDynamics\Festival {

  /**
   * Festival Utility
   *
   * @author Usability Dynamics
   */
  class Utility extends \UsabilityDynamics\Utility {
    
    /**
     * Returns expanded data of post
     *
     * @author peshkov@UD
     */
    static public function get_post_data( $id ) {
      global $wpdb;
    
      $post = wp_festival()->get_post( $id );
      
      if( $post ) {
      
        switch( $post[ 'post_type' ] ) {
      
          case 'artist':
            $perfomances = $wpdb->get_col( "
              SELECT post_id 
                FROM {$wpdb->postmeta} 
                WHERE meta_key = 'relatedArtists' 
                  AND meta_value = '{$id}'
            " );
            $post[ 'perfomances' ] = array();
            if( !empty( $perfomances ) ) {
              foreach( $perfomances as $perfomance_id ) {
                $perfomance = wp_festival()->get_post( $perfomance_id );
                if( $perfomance ) {
                  array_push( $post[ 'perfomances' ], $perfomance );
                }
              }
            }
            break;
        
        }
      
      }
      
      return $post;
    
    }
    
    /**
     * Prints styled bloginfo name
     *
     * @return type
     * @author Usability Dynamics
     * @since 0.1.0
     */
    static public function the_bloginfo_name() {
      $name = get_bloginfo( 'name', 'display' );
      echo $name;
    }
    
    /**
     * Set/reset excerpt filters: excerpt_length, excerpt_more
     *
     * Example:
     * $wp_escalade->set_excerpt_filter( '30', 'length' ); // Set excerpt length = 30
     * the_excerpt(); // Excerpt's length will be 30.
     * $wp_escalade->set_excerpt_filter( false, 'length' ); // Reset applied above filter.
     *
     * @staticvar array $_function
     *
     * @param mixed  $val
     * @param string $filter Available values: length, more
     *
     * @return boolean
     * @author Usability Dynamics
     * @since 0.1.0
     */
    static public function set_excerpt_filter( $val = false, $filter = 'length' ) {
      static $_function = array( 'excerpt_length' => '', 'excerpt_more' => '' );

      if( !in_array( $filter, array( 'length', 'more' ) ) ) {
        return false;
      }

      $_filter = 'excerpt_' . $filter;

      if( has_action( $_filter, $_function[ $_filter ] ) ) {
        remove_filter( $_filter, $_function[ $_filter ] );
      }

      if( !$val ) {
        $_function[ $_filter ] = '';

        return true;
      }

      $_function[ $_filter ] = create_function( '$val', 'return "' . $val . '";' );

      add_filter( $_filter, $_function[ $_filter ] );

      return true;
    }
    
    /**
     * Returns date of related Perfomance for passed Artist
     *
     * @author peshkov@UD
     */
    static public function get_artist_perfomance_date( $id ) {
      $date = false;
      $post = self::get_post_data( $id );
      
      // Try to find date
      if( !empty( $post[ 'perfomances' ] ) ) {
        foreach( $post[ 'perfomances' ] as $perfomance ) {
          if( !empty( $perfomance[ 'startDateTime' ] ) && ( time() < strtotime( $perfomance[ 'startDateTime' ] ) ) ) {
            $date = strtotime( $perfomance[ 'startDateTime' ] );
            break;
          }
        }
      }
      
      return $date;
      
    }
    
    /**
     * Returns image's url of specific Artist image ( 'featured', 'headshotImage', 'portraitImage', 'logoImage' )
     * If image is not found, try to return featured image
     *
     * @author peshkov@UD
     */
    static public function get_artist_image_link( $id, $args ) {
      $args = wp_parse_args( $args, array(
        'type' => 'featured', // Available values: 'featured', 'headshotImage', 'portraitImage', 'logoImage'
        'size' => 'full',
        'width' => '',
        'height' => '',
        'default' => true,
      ));
      
      $src = false;
      
      switch( $args[ 'type' ]  ) {
      
        case NULL:
        case 'featured':
          $src = self::get_image_link_by_post_id( $id, array(
            'size' => $args[ 'size' ],
            'width' => $args[ 'width' ],
            'height' => $args[ 'height' ],
            'default' => $args[ 'default' ],
          ) );
          break;
          
        default:
          $v = get_post_meta( $id, $args[ 'type' ], true );
          if( !empty( $v ) ) {
            $src = wp_festival()->get_image_link_by_attachment_id( $v, array(
              'size' => $args[ 'size' ],
              'width' => $args[ 'width' ],
              'height' => $args[ 'height' ],
            ) );
          }
          if( empty( $src ) ) {
            $src = self::get_image_link_by_post_id( $id, array(
              'size' => $args[ 'size' ],
              'width' => $args[ 'width' ],
              'height' => $args[ 'height' ],
              'default' => $args[ 'default' ],
            ) );
          }
          
          break;
      
      }
      
      return $src;
      
    }
    
    /**
     * Returns image's url of passed post ID 
     *
     * @see self::get_image_link_by_id()
     * @uses get_image_link_by_id()
     * @author peshkov@UD
     */
    static public function get_image_link_by_post_id( $id, $args = array() ) {
      $args = wp_parse_args( $args, array(
        'type' => 'post',
      ));
      return self::get_image_link_by_id( $id, $args );
    }
    
    /**
     * Returns image's url of passed attachment ID 
     *
     * @see self::get_image_link_by_id()
     * @uses get_image_link_by_id()
     * @author peshkov@UD
     */
    static public function get_image_link_by_attachment_id( $id, $args = array() ) {
      $args = wp_parse_args( $args, array(
        'type' => 'attachment',
      ));
      return self::get_image_link_by_id( $id, $args );
    }
    
    /**
     * Get dummy image if needed
     *
     * @author peshkov@UD
     */
    static public function get_no_image_link( $args = array() ) {
      $args = (object) wp_parse_args( $args, array(
        'size'    => 'large', // Get image by predefined image_size. If width and height are set - it's ignored.
        'width'   => '', // Custom size
        'height'  => '', // Custom size
      ));
      $url = false;
      if( !empty( $args->width ) && !empty( $args->height ) ) {
        $url = 'http://placehold.it/' . $args->width . 'x' . $args->height;
      } else {
        $sizes = \UsabilityDynamics\Utility::all_image_sizes();
        if( key_exists( $args->size, $sizes ) ) {
          $url = 'http://placehold.it/' . $sizes[ $args->size ][ 'width' ] . 'x' . $sizes[ $args->size ][ 'height' ];
        }
      }
      return $url;
    }
    
    /**
     * Returns post image's url with required size.
     *
     * Examples:
     * 1) wp_festival()->get_image_link_by_post_id( get_the_ID()); // Returns Full image
     * 2) wp_festival()->get_image_link_by_post_id( get_the_ID(), array( 'size' => 'medium' )); // Returns image with predefined size
     * 3) wp_festival()->get_image_link_by_post_id( get_the_ID(), array( 'width' => '430', 'height' => '125' )); // Returns image with custom size
     *
     * @param int          $id
     * @param array|string $args
     *
     * @global array       $wpp_query
     * @return string Returns false if image can not be returned
     * @author Usability Dynamics
     * @since 0.1.0
     */
    static public function get_image_link_by_id( $id, $args = array() ) {

      $args = (object) wp_parse_args( $args, array(
        'size'    => 'full', // Get image by predefined image_size. If width and height are set - it's ignored.
        'width'   => '', // Custom size
        'height'  => '', // Custom size
        'type'    => 'post', // Post or Attachment. Available values: 'post', 'attachment'
        // Optionals:
        'default' => true, // Use default image if images doesn't exist or not.
      ));

      if( $args->type == 'post' ) {
        if( has_post_thumbnail( $id ) ) {
          $attachment_id = get_post_thumbnail_id( $id );
        } else {
          // Use default image if image for post doesn't exist
          if( $args->default ) {
            return self::get_no_image_link( (array)$args );
          } else {
            return false;
          }
        }
      } else {
        $attachment_id = $id;
      }

      if( !empty( $args->width ) && !empty( $args->height ) ) {
        $_attachment = \UsabilityDynamics\Utility::get_image_link_with_custom_size( $attachment_id, $args->width, $args->height );
      } else {
        if( $args->size == 'full' ) {
          $_attachment          = wp_get_attachment_image_src( $attachment_id, $args->size );
          $_attachment[ 'url' ] = $_attachment[ 0 ];
        } else {
          $_attachment = \UsabilityDynamics\Utility::get_image_link( $attachment_id, $args->size );
        }
      }

      return is_wp_error( $_attachment ) ? false : $_attachment[ 'url' ];
    }

  }

}

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

    static public function get_post_data() {
    
      
    
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
     * Returns post image's url with required size.
     *
     * Examples:
     * 1) wp_festival()->get_image_link_by_post_id( get_the_ID()); // Returns Full image
     * 2) wp_festival()->get_image_link_by_post_id( get_the_ID(), array( 'size' => 'medium' )); // Returns image with predefined size
     * 3) wp_festival()->get_image_link_by_post_id( get_the_ID(), array( 'width' => '430', 'height' => '125' )); // Returns image with custom size
     *
     * @param int          $post_id
     * @param array|string $args
     *
     * @global array       $wpp_query
     * @return string Returns false if image can not be returned
     * @author Usability Dynamics
     * @since 0.1.0
     */
    static public function get_image_link_by_post_id( $post_id, $args = array() ) {

      $args = (object) wp_parse_args( $args, array(
        'size'    => 'large', // Get image by predefined image_size. If width and height are set - it's ignored.
        'width'   => '', // Custom size
        'height'  => '', // Custom size
        // Optionals:
        'default' => true, // Use default image if images doesn't exist or not.
      ));

      if( has_post_thumbnail( $post_id ) ) {
        $attachment_id = get_post_thumbnail_id( $post_id );
      } else {

        // Use default image if image for post doesn't exist
        if( $args->default ) {

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

        } else {
          return false;
        }
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

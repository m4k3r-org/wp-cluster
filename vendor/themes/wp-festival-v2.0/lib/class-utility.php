<?php
/**
 * Festival Utility
 *
 * @version 0.1.0
 * @author Usability Dynamics
 * @namespace UsabilityDynamics
 */
namespace UsabilityDynamics\Festival2 {

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

      $post = wp_festival2()->get_post( $id );

      if( $post ) {

        switch( $post[ 'post_type' ] ) {

          case 'artist':
            //** Get related perfomances */
            $perfomances = $wpdb->get_col( "
              SELECT post_id
                FROM {$wpdb->postmeta}
                WHERE meta_key = 'relatedArtists'
                  AND meta_value = '{$id}'
            " );
            $post[ 'perfomances' ] = array();
            if( !empty( $perfomances ) ) {
              foreach( $perfomances as $perfomance_id ) {
                $perfomance = wp_festival2()->get_post( $perfomance_id );
                if( $perfomance ) {
                  array_push( $post[ 'perfomances' ], $perfomance );
                }
              }
            }
            //** Try to get IDs for Social Streams */
            $post[ 'socialStreams' ] = array();
            if( !empty( $post[ 'socialLinks' ] ) && is_array( $post[ 'socialLinks' ] ) ) {
              foreach( $post[ 'socialLinks' ] as $link ) {
                if( is_string( $link ) && $sdata = self::maybe_get_sn_data( $link ) ) {
                  $post[ 'socialStreams' ][ $sdata[ 'network' ] ] = $sdata[ 'uid' ];
                }
              }
            }
            break;

        }

      }

      return $post;

    }
    
    /**
     * Fixes our paths for our modules
     */
    public function plugins_url( $url ){
      /** If we have a base directory defined, we should replace the root path */
      if( defined( 'WP_BASE_DIR' ) ){
        $url = str_ireplace( WP_BASE_DIR, '', $url );
      }

      /** Now if we have 'vendor' unnecessaries, we should replace some paths */
      $url = str_ireplace( 'vendor/plugins/vendor/themes', 'vendor/themes', $url );
      /** Return some others */
      $url = str_ireplace( '/modules/var/www/themes/wp-festival-2/', '/themes/wp-festival-2/', $url );
      
      /** If we're fixing the carrington build URL */
      if( stripos( $url, 'carrington' ) ){
        $url = substr( $url, stripos( $url, '/lib-carrington/' ) );
        $url = get_template_directory_uri() . '/vendor/libraries/usabilitydynamics' . $url;
      }

      return $url;
    }

    /**
     * Parses social url link and returns network, username and username ID if success.
     *
     * @param $url
     *
     * @return array
     * @author peshkov@UD
     * @since 0.1.0
     */
    static public function maybe_get_sn_data( $url ) {

      //** Try to get username from url */
      preg_match( "#[^\/]*$#", $url, $matches );
      if( empty( $matches[0] ) ) {
        return false;
      }

      $data = array(
        'network' => '',
        'username' => $matches[0],
        'uid' => $matches[0], // In some cases username == uid
      );

      //** Try to determine social network and get username ID */
      switch( true ) {

        //* YOUTUBE */
        case ( strpos( $url, 'youtube' ) !== false ):
          $data[ 'network' ] = 'youtube';
          break;

        //* TWITTER */
        case ( strpos( $url, 'twitter' ) !== false ):
          $data[ 'network' ] = 'twitter';
          break;

        //* FACEBOOK */
        case ( strpos( $url, 'facebook' ) !== false ):
          $data[ 'network' ] = 'facebook';
          //** Try to get information about user from facebook by username */
          $r = wp_safe_remote_get( "http://graph.facebook.com/{$data[ 'username' ]}" );
          //* Validate our response */
          if(
            empty( $r[ 'body' ] ) ||
            !( $d = json_decode( $r[ 'body' ], true ) ) ||
            !isset( $d[ 'id' ] )
          ) {
            return false;
          }
          $data[ 'uid' ] = $d[ 'id' ];
          break;

        //* INSTAGRAM */
        case ( strpos( $url, 'instagram' ) !== false ):
          $data[ 'network' ] = 'instagram';
          //** Determine if we have access_token */
          //$access_token = wp_festival2()->get( 'configuration.social_stream.instagram.access_token' );

          define( 'WP_SOCIAL_STREAM_INSTAGRAM_ACCESS_TOKEN', defined( 'WP_SOCIAL_STREAM_INSTAGRAM_ACCESS_TOKEN' ) ? WP_SOCIAL_STREAM_INSTAGRAM_ACCESS_TOKEN : '44220099.ec4c95b.d2c3acc28b1f432884be1ddcd8733499' );

          $access_token = WP_SOCIAL_STREAM_INSTAGRAM_ACCESS_TOKEN;
		  
          if( empty( $access_token ) ) {
            return false;
          }
          //** Search for user with the similar username */
          $r = wp_safe_remote_get( "https://api.instagram.com/v1/users/search?q={$data[ 'username' ]}&access_token={$access_token}" );
          //* Validate our response */
          if(
            empty( $r[ 'body' ] ) ||
            !( $d = json_decode( $r[ 'body' ], true ) ) ||
            !isset( $d[ 'meta' ][ 'code' ] ) ||
            $d[ 'meta' ][ 'code' ] !== 200 ||
            empty( $d[ 'data' ] ) ||
            !is_array( $d[ 'data' ] )
          ) {
            return false;
          }
          //** Now, go through the response and try to get our user. */
          foreach ( $d[ 'data' ] as $user ) {
            if( $user[ 'username' ] == $data[ 'username' ] ) {
              if( !isset( $user[ 'id' ] ) ) {
                return false;
              }
              $data[ 'uid' ] = $user[ 'id' ];
              break;
            }
          }
          break;

      }

      return $data;

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
            $src = wp_festival2()->get_image_link_by_attachment_id( $v, array(
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
     * 1) wp_festival2()->get_image_link_by_post_id( get_the_ID()); // Returns Full image
     * 2) wp_festival2()->get_image_link_by_post_id( get_the_ID(), array( 'size' => 'medium' )); // Returns image with predefined size
     * 3) wp_festival2()->get_image_link_by_post_id( get_the_ID(), array( 'width' => '430', 'height' => '125' )); // Returns image with custom size
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
        'crop'    => false,
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
        $_attachment = \UsabilityDynamics\Utility::get_image_link_with_custom_size( $attachment_id, $args->width, $args->height, $args->crop );
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

<?php
/**
 *
 * @author Andy Potanin <andy.potanin@usabilitydynamics.com>
 */
namespace wpCloud\Vertical {

  class EDM {

    /**
     * Version of child theme
     *
     * @public
     * @property $version
     * @var string
     */
    public $version = '2.0.4';

    function __construct() {

      // Define Data Structure
      // - add_post_type_support( 'event', 'post-formats' );
      // - set_post_format();

      // Define User Roles and Capabilities
      // add_user_role( );

      // Define Post Object Callbacks
      // - add_filter( 'wp-elastic:websiteLink', array( 'wpCloud\Vertical\EDM::Utility', 'get_image_urls' ) );

      // migrated out of wp-festival
      // $file = WP_BASE_DIR . '/static/schemas/default.settings.json';

    }

    /**
     * Apply a method to multiple filters
     *
     * @param $tags
     * @param $function
     */
    public function add_filters( $tags, $function ) {

      foreach( $tags as $tag ) {
        add_filter( $tag, $function );
      }

    }

  }

}
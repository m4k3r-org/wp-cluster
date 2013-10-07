<?php
/**
 * Media Access Controller
 *
 * @module Veneer
 * @author potanin@UD
 */
namespace Veneer {

  /**
   * Class Media
   *
   * @module Veneer
   */
  class Media {

    /**
     * Initialize Media
     *
     * @for Media
     */
    public function __construct() {

      //** Support for custom uploads directory */
      if( defined( 'WP_MEDIA_PATH' ) ) {
        add_filter( 'pre_option_upload_path', create_function( '', ' return WP_MEDIA_PATH; ' ));
        add_filter( 'pre_option_upload_url_path', create_function( '', ' return WP_MEDIA_URL; ' ));
      }

    }

  }
}
<?php
/**
 * Media Access Controller
 *
 * @version 0.1.5
 * @module Veneer
 * @author potanin@UD
 */
namespace UsabilityDynamics\Veneer {

  if( !class_exists( 'UsabilityDynamics\Veneer\Media' ) ) {

    /**
     * Class Media
     *
     * @module Veneer
     */
    class Media {

      /**
       * Absolute path to site-specific file directory
       *
       * @public
       * @static
       * @property $path
       * @type {Object}
       */
      public $path = null;

      /**
       * Initialize Media
       *
       * @for Media
       */
      public function __construct() {

        $this->path = BLOGUPLOADDIR;

        //** Support for custom uploads directory */
        if( defined( 'WP_MEDIA_PATH' ) ) {
          // add_filter( 'pre_option_upload_path', create_function( '', ' return WP_MEDIA_PATH; ' ) );
          // add_filter( 'pre_option_upload_url_path', create_function( '', ' return WP_MEDIA_URL; ' ) );
        }

      }

    }

  }

}
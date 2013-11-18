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

        // Primary image path/url override.
        add_filter( 'upload_dir', array( get_class(), 'upload_dir' ));

        $wp_upload_dir = wp_upload_dir();

        $this->directory = BLOGUPLOADDIR;
        $this->path      = $wp_upload_dir[ 'path' ];
        $this->url       = $wp_upload_dir[ 'url' ];
        $this->basedir   = $wp_upload_dir[ 'basedir' ];
        $this->baseurl   = $wp_upload_dir[ 'baseurl' ];
        $this->domain    = defined( 'WP_VENEER_DOMAIN_MEDIA' ) && WP_VENEER_DOMAIN_MEDIA ? undefined : $wp_upload_dir[ 'baseurl' ];

      }

      /**
       *
       *
       * @param $settings
       * @param $settings.path
       * @param $settings.url
       * @param $settings.subdir
       * @param $settings.basedir
       * @param $settings.baseurl
       * @param $settings.error
       */
      public static function upload_dir( $settings ) {

        $_instance = Bootstrap::get_instance();

        // @todo Add hookin to override media path here to a subdomain.
        // http://demo-site.loc/files to http://demo-site.loc/media
        $settings[ 'baseurl' ] = ( is_ssl() ? 'https://' : 'http://' ) . untrailingslashit( $_instance->domain ) . '/media';

        // Change http://demo-site.loc/files/2013/11 to http://demo-site.loc/media/2013/11
        $settings[ 'url' ] = str_replace( '/files/', '/media/', $settings[ 'url' ] );

        return $settings;

      }

    }

  }

}
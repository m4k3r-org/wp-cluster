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

        // Get media/upload vales. (wp_upload_dir() will generate directories).
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
       * @todo Add hookin to override media path here to a subdomain.
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

        // If network main stie.
        if ( is_main_site() ) {
          $settings[ 'path' ] = str_replace( '/uploads', '/storage/' . $_instance->domain, $settings[ 'path' ] );
          $settings[ 'basedir' ] = str_replace( '/uploads', '/storage/' . $_instance->domain, $settings[ 'basedir' ] );
          $settings[ 'baseurl' ] = str_replace( '/uploads', '/media/', $settings[ 'baseurl' ] );
          $settings[ 'url' ] = str_replace( '/uploads', '/media', $settings[ 'url' ] );
        }

        // If network main stie.
        if ( !is_main_site() ) {
          $settings[ 'baseurl' ] = ( is_ssl() ? 'https://' : 'http://' ) . untrailingslashit( $_instance->domain ) . '/media';
          $settings[ 'url' ] = str_replace( '/files/', '/media/', $settings[ 'url' ] );
        }

        return $settings;

      }

    }

  }

}
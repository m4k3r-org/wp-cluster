<?php
/**
 * Media Access Controller
 *
 * @version 0.1.5
 * @module Cluster
 * @author potanin@UD
 */
namespace UsabilityDynamics\Cluster {

  if( !class_exists( 'UsabilityDynamics\Cluster\Media' ) ) {

    /**
     * Class Media
     *
     * @todo When CDN is disabled some images seem to use the network's domain as the path.
     *
     * @module Cluster
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
       * Clean domain.
       *
       * @public
       * @static
       * @property $domain
       * @type {Object}
       */
      public $domain = null;

      /**
       * Initialize Media
       *
       * @for Media
       */
      public function __construct() {
        global $cluster;

        if( !defined( 'UPLOADBLOGSDIR' ) ) {
          wp_die( '<h1>Network Error</h1><p>Unable to instatiate media the UPLOADBLOGSDIR constant is not defined.</p>' );
        }

        // @todo Enable to replace media paths with subdomain path.
        // $cluster->cdn = array( "subdomain" => "media" );

        // Primary image path/url override.
        add_filter( 'upload_dir', array( &$this, 'upload_dir' ) );

        // Get media/upload vales. (wp_upload_dir() will generate directories).
        $wp_upload_dir    = wp_upload_dir();
        $this->directory = BLOGUPLOADDIR;
        $this->path      = $wp_upload_dir[ 'path' ];
        $this->url       = $wp_upload_dir[ 'url' ];
        $this->basedir   = $wp_upload_dir[ 'basedir' ];
        $this->baseurl   = $wp_upload_dir[ 'baseurl' ];
        $this->domain    = defined( 'WP_VENEER_DOMAIN_MEDIA' ) && WP_VENEER_DOMAIN_MEDIA ? null : $wp_upload_dir[ 'baseurl' ];

      }

      /**
       *
       * @todo Add hookin to override media path here to a subdomain.
       *
       * @param $settings
       * @param $settings .path
       * @param $settings .url
       * @param $settings .subdir
       * @param $settings .basedir
       * @param $settings .baseurl
       * @param $settings .error
       */
      public static function upload_dir( $settings ) {
        global $cluster;

        $_instance = Bootstrap::get_instance();

        // If Currently on Network Main Site. (Which is unlikely).
        if( is_main_site() ) {
          $settings[ 'path' ]    = str_replace( '/uploads', '/' . UPLOADBLOGSDIR . '/' . $_instance->domain, $settings[ 'path' ] );
          $settings[ 'basedir' ] = str_replace( '/uploads', '/' . UPLOADBLOGSDIR . '/' . $_instance->domain, $settings[ 'basedir' ] );
          $settings[ 'baseurl' ] = str_replace( '/uploads', '/media/', $settings[ 'baseurl' ] );
          $settings[ 'url' ]     = str_replace( '/uploads', '/media', $settings[ 'url' ] );
        }

        // If On Standard Site.
        if( !is_main_site() ) {
          $settings[ 'baseurl' ] = ( is_ssl() ? 'https://' : 'http://' ) . untrailingslashit( $_instance->domain ) . '/media';
          $settings[ 'url' ]     = str_replace( '/files/', '/media/', $settings[ 'url' ] );
        }

        // CDN Media Redirection.
        if( $cluster->get( 'cdn.active' ) ) {

          // Strip Media from Pathname.
          $settings[ 'baseurl' ] = str_replace( '/media', '', $settings[ 'baseurl' ] );
          $settings[ 'url' ] = str_replace( '/media', '', $settings[ 'url' ] );

          // Add media Subdomain. @todo use $cluster->cdn[ 'subdomain' ]
          $settings[ 'baseurl' ] = str_replace( '://', '://' . $cluster->get( 'cdn.subdomain' ) . '.', $settings[ 'baseurl' ] );
          $settings[ 'url' ] = str_replace( '://', '://' . $cluster->get( 'cdn.subdomain' ) . '.', $settings[ 'url' ] );

        }

        return $settings;

      }

    }

  }

}
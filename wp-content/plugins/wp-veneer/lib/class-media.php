<?php
/**
 * Media Access Controller
 *
 * @version 0.1.5
 * @module Cluster
 * @author potanin@UD
 */
namespace UsabilityDynamics\Veneer {

  if( !class_exists( 'UsabilityDynamics\Veneer\Media' ) ) {

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
       * Custom URL Base for all media.
       *
       * @public
       * @static
       * @property $url_rewrite
       * @type {Object}
       */
      public $url_rewrite = null;

      /**
       * Instance Domain.
       *
       * @public
       * @static
       * @property $site
       * @type {String}
       */
      public $site = null;

      /**
       * Instance Site ID.
       *
       * @public
       * @static
       * @property $site_id
       * @type {Integer}
       */
      public $site_id = null;

      /**
       * Media Active.
       *
       * @public
       * @property $active
       * @type {Boolean}
       */
      public $active = false;

      /**
       * Initialize Media
       *
       * @for Media
       */
      public function __construct( $args ) {
        global $wp_veneer, $wpdb;

        // Enable MS Site Rewriting.
        add_filter( 'default_site_option_ms_files_rewriting', array( $this, '_ms_files_rewriting' ) );
        //add_filter( 'pre_option_upload_path', array( $this, '_media_path' ) );
        add_filter( 'pre_option_upload_url_path', array( $this, '_media_url_path' ) );

	      add_filter( 'option_upload_path', array( $this, '_media_path' ) );

        // Extend Arguments with defaults.
        $args = Utility::parse_args( $args, array(
          "active"    => true,
          "subdomain" => "media",
          "cdn"       => array(),
          "url_rewrite"  => get_option( 'upload_url_path' )
        ));

        $this->site     = $wp_veneer->site;
        $this->site_id  = $wp_veneer->site_id;
        $this->cluster  = defined( 'WP_BASE_DOMAIN' ) ? WP_BASE_DOMAIN : null;

        if( defined( 'MULTISITE' ) && MULTISITE && $wpdb->site ) {
          $this->network   = $wpdb->get_var( "SELECT domain FROM {$wpdb->site} WHERE id = {$wpdb->siteid}" );
        }

        if( $args->subdomain ) {
          $this->subdomain = $args->subdomain;
        }
        if( $args->url_rewrite ) {
          $this->url_rewrite = $args->url_rewrite;
        }

        if( $args->cdn ) {
          $this->cdn = $args->cdn;
        }

        if( $args->active ) {
          $this->active = $args->active;
        }

        // Trying to maintain semi-native support.
        if( !defined( 'UPLOADBLOGSDIR' ) ) {
          // define( 'UPLOADBLOGSDIR', defined( 'WP_VENEER_STORAGE' ) ? WP_VENEER_STORAGE : 'static/storage' );
        }

        // Uploads path relative to ABSPATH
        if( !defined( 'BLOGUPLOADDIR' ) ) {
          // define( 'BLOGUPLOADDIR', UPLOADBLOGSDIR . '/media' );
        }

        // Primary image path/url override.
        add_filter( 'upload_dir', array( $this, 'upload_dir' ) );

        // Get media/upload vales. (wp_upload_dir() will generate directories).
        $wp_upload_dir   = wp_upload_dir();
        $this->path      = $wp_upload_dir[ 'path' ];
        $this->url       = $wp_upload_dir[ 'url' ];
        $this->basedir   = $wp_upload_dir[ 'basedir' ];
        $this->baseurl   = $wp_upload_dir[ 'baseurl' ];

        //$this->directory = defined( 'BLOGUPLOADDIR' ) && BLOGUPLOADDIR ? BLOGUPLOADDIR : '';
        //$this->domain    = defined( 'WP_VENEER_STORAGE' ) && WP_VENEER_STORAGE ? null : $wp_upload_dir[ 'baseurl' ];

        // die( json_encode( $this->_debug() ) );

      }

      /**
       * Return URL Mapping Array
       *
       * @return array
       */
      private function _debug() {
        global $wp_veneer;

        return array(
          'wp_upload_dir' => wp_upload_dir(),
          'this'          => $this,
          'wp_veneer'     => $wp_veneer
        );

      }

      /**
       * Do nothing.
       *
       * @param $default
       *
       * @return mixed
       */
      public function _media_url_path( $default ) {
        return $default;
      }

      /**
       * Returns the base non-site-specific path for file uplods. Final path computed in wp_upload_dir();
       *
       * @note This method seems to break things when using switch_to_blog yet with it disabled things seem to work. -potanin@UD
       *
       * @param $default
       * @return string
       */
      public function _media_path( $default ) {

	      if( $default ) {
		      return wp_normalize_path( $default . DIRECTORY_SEPARATOR . 'media' );
	      }

        if( defined( 'WP_VENEER_PUBLIC' ) && WP_VENEER_PUBLIC ) {
          $_public_path = trailingslashit( WP_VENEER_PUBLIC );
        } elseif( defined( 'WP_VENEER_STORAGE' ) && WP_VENEER_STORAGE ) {
          $_public_path = trailingslashit( WP_VENEER_STORAGE );
        } else {
          $_public_path = trailingslashit( WP_CONTENT_DIR );
        }

        // Append the apex domain to storage path.
        if( defined( 'MULTISITE' ) && MULTISITE && ( defined( 'WP_VENEER_PUBLIC' ) && WP_VENEER_PUBLIC || defined( 'WP_VENEER_STORAGE' ) && WP_VENEER_STORAGE ) ) {
          $_public_path = $_public_path . trailingslashit( $this->site );
        }

        return wp_normalize_path( $_public_path . DIRECTORY_SEPARATOR . 'media' );

      }

      /**
       *
       * @param $default
       *
       * @return bool
       */
      public function _ms_files_rewriting( $default ) {
        return true;
      }

      /**
       * Media Paths and URLs
       *
       * @version 2.0.0
       * @param $settings
       * @param $settings .path
       * @param $settings .url
       * @param $settings .subdir
       * @param $settings .basedir
       * @param $settings .baseurl
       * @param $settings .error
       */
      public function upload_dir( $settings ) {
        global $wp_veneer;

        if( defined( 'WP_VENEER_STORAGE' ) ) {
          $settings[ 'path' ]    = str_replace( '/blogs.dir/' . $this->site_id . '/files', '/' . WP_VENEER_STORAGE . '/' . $this->site . '/media', $settings[ 'path' ] );
          $settings[ 'basedir' ] = str_replace( '/blogs.dir/' . $this->site_id . '/files', '/' . WP_VENEER_STORAGE . '/' . $this->site . '/media', $settings[ 'basedir' ] );

          $settings[ 'path' ]    = str_replace( '/sites/' . $this->site_id . '', '/' . $this->site . '/media', $settings[ 'path' ] );
          $settings[ 'basedir' ] = str_replace( '/sites/' . $this->site_id . '', '/' . $this->site . '/media', $settings[ 'basedir' ] );

          if( !$this->url_rewrite ) {
            $settings[ 'url' ] = home_url( '/media' . $settings[ 'subdir' ] );
            $settings[ 'baseurl' ] = home_url( '/media' );
          }

          if( $this->url_rewrite ) {
            $_rewrite = untrailingslashit( $this->url_rewrite );
            $settings[ 'url' ] = str_replace( 'http://' . $this->site . '/files', $_rewrite, $settings[ 'url' ] );
            $settings[ 'baseurl' ] = str_replace( 'http://' . $this->site . '/files', $_rewrite, $settings[ 'baseurl' ] );

            $settings[ 'url' ] = str_replace( '/sites/' . $this->site_id . '', '/media', $settings[ 'url' ] );
            $settings[ 'baseurl' ] = str_replace( '/sites/' . $this->site_id . '', '/media', $settings[ 'baseurl' ] );
          }

        }

        // CDN Media Redirection.
        if( !$this->url_rewrite && $wp_veneer->get( 'media.cdn.active' ) ) {

          // Strip Media from Pathname.
          // $settings[ 'baseurl' ] = str_replace( '/media', '', $settings[ 'baseurl' ] );
          // $settings[ 'url' ]     = str_replace( '/media', '', $settings[ 'url' ] );

          // Add media Subdomain.
          // $settings[ 'baseurl' ] = str_replace( '://', '://' . $wp_veneer->get( 'media.subdomain' ) . '.', $settings[ 'baseurl' ] );
          // $settings[ 'url' ]     = str_replace( '://', '://' . $wp_veneer->get( 'media.subdomain' ) . '.', $settings[ 'url' ] );

        }

        if( get_option( 'upload_url_path' ) ) {
          $settings[ 'url' ] = get_option( 'upload_url_path' );
          $settings[ 'baseurl' ] = get_option( 'upload_url_path' );
        }

        return $settings;

      }

    }

  }

}
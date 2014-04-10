<?php
/**
 * Theme Access Controller
 *
 * @module Cluster
 * @author potanin@UD
 */
namespace UsabilityDynamics\Cluster {

  if( !class_exists( 'UsabilityDynamics\Cluster\Theme' ) ) {

    /**
     * Class Theme
     *
     * @module Cluster
     */
    class Theme {

      /**
       * Theme Exists.
       *
       * @public
       * @property $exists
       * @type {Boolean}
       */
      public $exists = null;

      /**
       * Theme Directories.
       *
       * @public
       * @property $directories
       * @type {Array}
       */
      public $directories = Array();

      /**
       * WordPrsss WP_Theme Instance
       *
       * @private
       * @property $active
       * @type {WP_Theme}
       */
      private $active = null;

      /**
       * Initialize Theme
       *
       * @for Theme
       */
      public function __construct() {
        global $wp_cluster;

        if( defined( 'WP_BASE_DIR' ) && is_dir( WP_BASE_DIR . DIRECTORY_SEPARATOR . 'themes' ) ) {
          //register_theme_directory( WP_BASE_DIR . DIRECTORY_SEPARATOR . 'themes' );
        }

        // $this->add_site_directories();

        // Get WP_Theme Instance.
        $this->active = wp_get_theme();
        $this->exists = $this->active->exists();
        $this->domain = $wp_cluster->domain;
        $this->site_name = $wp_cluster->site_name;

        add_action( 'template_redirect', array( &$this, 'template_redirect' ) );

      }

      /**
       * Output Fatal Error Message
       *
       * @param $message
       */
      public function fatal( $message ) {
        wp_die( '<h1>' . $this->site_name . '</h1><p>' . $message . '</p>', $this->domain );
      }

      /**
       * Check Theme Existance.
       *
       */
      public function template_redirect() {

        if( !$this->exists ) {
          self::fatal( 'Our apologies, but this site is not yet set up. Please check back soon.' );
        }

      }

      /**
       * Scan all blog file directories and look for /themes/ directory
       *
       * @todo Fix error with using $wpdb->blogs.
       * @todo Should probably have them automatically enabled for the respective blog.
       *
       * @method add_site_directories
       */
      private function add_site_directories() {
        global $wpdb;

        if( is_dir( WP_CONTENT_DIR . '/themes-client' ) ) {
          $this->directories[ ] = WP_CONTENT_DIR . '/themes-client';
        }

        foreach( $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" ) as $blog_id ) {

          $_upload_path = get_blog_option( $blog_id, 'upload_path' );

          if( $_upload_path && $_blog_theme_directory = str_replace( 'files', 'themes', WP_BASE_DIR . '/' . $_upload_path ) ) {

            if( is_dir( $_blog_theme_directory ) ) {
              $this->directories[ ] = $_blog_theme_directory;

            }

          }

        }

        foreach( (array) $this->directories as $directory ) {
          register_theme_directory( $directory );
        }

      }

    }

  }

}
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
       * WordPrsss WP_Theme Instance
       *
       * @private
       * @property $_theme
       * @type {WP_Theme}
       */
      private $_theme = null;

      /**
       * Initialize Theme
       *
       * @for Theme
       */
      public function __construct() {

        if( defined( 'WP_PRIMARY_THEME_DIR' ) && is_dir( WP_BASE_DIR . DIRECTORY_SEPARATOR . 'network-themes' ) ) {
          add_filter( 'template_directory', create_function( '', ' return WP_PRIMARY_THEME_DIR; ' ) );
          add_filter( 'stylesheet_directory', create_function( '', ' return WP_PRIMARY_THEME_URL; ' ) );
          add_filter( 'template_directory_uri', create_function( '', ' return WP_PRIMARY_THEME_URL; ' ) );
          add_filter( 'stylesheet_directory_uri', create_function( '', ' return WP_PRIMARY_THEME_URL; ' ) );
        }

        if( defined( 'WP_BASE_DIR' ) && is_dir( WP_BASE_DIR . DIRECTORY_SEPARATOR . 'network-themes' ) ) {
          register_theme_directory( WP_BASE_DIR . DIRECTORY_SEPARATOR . 'network-themes' );
        }

        // $this->add_site_directories();

        // Get WP_Theme Instance.
        $this->_theme = wp_get_theme();

        $this->exists = $this->_theme->exists();

        add_action( 'template_redirect', array( &$this, 'template_redirect' ) );

      }

      /**
       * Output Fatal Error Message
       *
       * @param $message
       */
      public function fatal( $message ) {
        wp_die( '<h1>Cluster Theme Error</h1><p>' . $message . '</p>' );
      }

      /**
       * Check Theme Existance.
       *
       */
      public function template_redirect() {

        if( !$this->exists ) {
          self::fatal( 'The site is operational but the configured theme does not exist.' );
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
          $this->theme_directories[ ] = WP_CONTENT_DIR . '/themes-client';
        }

        foreach( $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" ) as $blog_id ) {

          $_upload_path = get_blog_option( $blog_id, 'upload_path' );

          if( $_upload_path && $_blog_theme_directory = str_replace( 'files', 'themes', WP_BASE_DIR . '/' . $_upload_path ) ) {

            if( is_dir( $_blog_theme_directory ) ) {
              $this->theme_directories[ ] = $_blog_theme_directory;

            }

          }

        }

        foreach( (array) $this->theme_directories as $directory ) {
          register_theme_directory( $directory );
        }

      }

    }

  }

}
<?php
/**
 * Theme Access Controller
 *
 * @module Veneer
 * @author potanin@UD
 */
namespace Veneer {

  /**
   * Class Theme
   *
   * @module Veneer
   */
  class Theme {

    /**
     * Initialize Theme
     *
     * @for Theme
     */
    public function __construct() {
      // global $wp_theme_directories;

      //** Support for custom theme directory */
      if ( defined( 'WP_PRIMARY_THEME_DIR' ) && is_dir( WP_BASE_DIR . DIRECTORY_SEPARATOR . 'network-themes' ) ) {
        add_filter( 'template_directory', create_function( '', ' return WP_PRIMARY_THEME_DIR; ' ) );
        add_filter( 'stylesheet_directory', create_function( '', ' return WP_PRIMARY_THEME_URL; ' ) );
        add_filter( 'template_directory_uri', create_function( '', ' return WP_PRIMARY_THEME_URL; ' ) );
        add_filter( 'stylesheet_directory_uri', create_function( '', ' return WP_PRIMARY_THEME_URL; ' ) );
      }

      if ( defined( 'WP_BASE_DIR' ) && is_dir( WP_BASE_DIR . DIRECTORY_SEPARATOR . 'network-themes' ) ) {
        register_theme_directory( WP_BASE_DIR . DIRECTORY_SEPARATOR . 'network-themes' );
      }

      $this->add_site_directories();

      // die( '<pre>' . print_r( $wp_theme_directories, true ) . '</pre>' );

    }

    /**
     * Scan all blog file directories and look for /themes/ directory
     *
     * @method add_site_directories
     * @todo Should probably have them automatically enabled for the respective blog.
     */
    public function add_site_directories() {
      global $wpdb, $_varnish;

      if ( is_dir( WP_CONTENT_DIR . '/themes-client' ) ) {
        $_varnish[ 'theme_directories' ][ ] = WP_CONTENT_DIR . '/themes-client';
      }

      foreach ( $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" ) as $blog_id ) {

        $_upload_path = get_blog_option( $blog_id, 'upload_path' );

        if ( $_upload_path && $_blog_theme_directory = str_replace( 'files', 'themes', WP_BASE_DIR . '/' . $_upload_path ) ) {

          if ( is_dir( $_blog_theme_directory ) ) {
            $_varnish[ 'theme_directories' ][ ] = $_blog_theme_directory;

          }

        }

      }

      foreach ( (array) $_varnish[ 'theme_directories' ] as $directory ) {
        register_theme_directory( $directory );
      }

    }

  }
}
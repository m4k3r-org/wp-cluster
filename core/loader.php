<?php
/**
 * Varnish CMS Loader
 *
 * The Loder class is self-initializing.
 *
 * @namespace Varnish
 * @module Varnish
 */
namespace Varnish {

  /**
   * Class Loader
   *
   * @class Loader
   */
  class Loader {

    /**
     * Initialize VarnishCMS Loader
     *
     * @for Loader
     * @method __construct
     */
    public function __construct() {

      // add_action( 'admin_menu', array( 'Developer', 'admin_menu' ) );

      if( file_exists( WPMU_PLUGIN_DIR . '/regenerate-thumbnails/regenerate-thumbnails.php' ) ) {
        require WPMU_PLUGIN_DIR . '/regenerate-thumbnails/regenerate-thumbnails.php';
      }

      if( file_exists( WPMU_PLUGIN_DIR . '/akismet/akismet.php' ) ) {
        require WPMU_PLUGIN_DIR . '/akismet/akismet.php';
      }

      if( file_exists( WPMU_PLUGIN_DIR . '/wp-mail-smtp/wp_mail_smtp.php' ) ) {
        require WPMU_PLUGIN_DIR . '/wp-mail-smtp/wp_mail_smtp.php';
      }

      if( file_exists( WPMU_PLUGIN_DIR . '/wp-simplify/wp-simplify.php' ) ) {
        require WPMU_PLUGIN_DIR . '/wp-simplify/wp-simplify.php';
      }

      //** Support for custom uploads directory */
      if( defined( 'WP_MEDIA_PATH' ) ) {
        add_filter( 'pre_option_upload_path', create_function( '', ' return WP_MEDIA_PATH; ' ));
        add_filter( 'pre_option_upload_url_path', create_function( '', ' return WP_MEDIA_URL; ' ));
      }


      /**
       * No good reason for MS to be disabled aside from testing.
       *
       */
      if( is_multisite() ) {

        require_once( WPMU_PLUGIN_DIR . '/lib/class.varnish_cms.php' );

        Core::fix_urls();

        Core::add_theme_directories();

        Core::load_mu_plugins();

        Core::initialize();

        //** Support for custom theme directory */
        if( defined( 'WP_PRIMARY_THEME_DIR' ) ) {
          add_filter( 'template_directory', create_function( '', ' return WP_PRIMARY_THEME_DIR; ' ));
          add_filter( 'stylesheet_directory', create_function( '', ' return WP_PRIMARY_THEME_URL; ' ));
          add_filter( 'template_directory_uri', create_function( '', ' return WP_PRIMARY_THEME_URL; ' ));
          add_filter( 'stylesheet_directory_uri', create_function( '', ' return WP_PRIMARY_THEME_URL; ' ));
        }

        //** Support for custom uploads directory */
        if( defined( 'WP_FILES_PATH' ) && defined( 'WP_FILES_URL' ) ) {
          add_filter( 'pre_option_upload_path', create_function( '', ' return WP_FILES_PATH; ' ));
          add_filter( 'pre_option_upload_url_path', create_function( '', ' return WP_FILES_URL; ' ));
        }

      }


    }

  }

  // Initialize VarnishCMS
  new Loader();

}
<?php
/**
 * Legacy Support
 *
 * @namespace Flawless
 * @class Flawless\Shortcode
 *
 * @user: potanin@UD
 * @date: 8/31/13
 * @time: 10:33 AM
 */
namespace Flawless {

  /**
   * Legacy Support
   *
   * -
   *
   * @module Flawless
   * @class Legacy
   */
  class Legacy {

    // @parameters $version Version of class.
    public $version = '0.0.2';

    /**
     * Class Constructor
     *
     */
    public function __construct( $params = array() ) {

      // Bail early if old server.
      if ( version_compare( phpversion(), 5.3 ) < 0 ) {
        switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
        return wp_die( sprintf( __( 'Your version of PHP, %1s, is old, and this theme cannot support it, so it has been disabled. Please consider upgrading to 5.3, or newer. <a href="%2s">Back to Safety.</a>', HDDP ), phpversion(), admin_url() ) );
      }

      //** Load defaults on theme activation */
      if ( current_user_can( 'update_themes' ) ) {
        add_action( 'flawless::admin_init', array( $this, 'admin_init' ) );
      }

    }

    /**
     * Handle upgrading the theme.
     *
     * Only displayed to users who can Update Themes.
     *
     * @method admin_init
     * @for Legacy
     *
     * @since 0.0.2
     */
    static function admin_init( &$flawless ) {

      /** @var $redirect boolean */
      $redirect = null;

      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

      // If new install.
      if( !$flawless->get( 'version' ) ) {
        $redirect = apply_filters( 'flawless::install', add_query_arg( 'admin_splash_screen', 'welcome', Flawless_Admin_URL ), $flawless );
      }

      //** If upgrading from older version */
      if ( version_compare( Flawless_Core_Version, $flawless->get( 'version' ), '>' ) ) {
        $redirect = apply_filters( 'flawless::update', add_query_arg( 'admin_splash_screen', 'updated', Flawless_Core_Version ), $flawless );
      }

      // Run the update now in case we have a redirection
      if ( $redirect ) {
        $flawless->set( 'version', Flawless_Core_Version );
        die( wp_redirect( $redirect ) );

      }

    }

  }

}
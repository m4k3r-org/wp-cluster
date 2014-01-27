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
namespace UsabilityDynamics\Flawless {

  /**
   * Legacy Support
   *
   * -
   *
   * @module Flawless
   * @class Legacy
   */
  class Legacy {

    /**
     * Legacy Class version.
     *
     * @public
     * @static
     * @property $version
     * @type {Object}
     */
    public static $version = '0.0.2';

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
     * Dump of Old Stuff
     *
     *
     */
    public function dump() {

      // JavaScript Assets. @from init_upper
      // wp_register_script( 'require', '//cdnjs.cloudflare.com/ajax/libs/require.js/2.1.8/require.min.js', array(), '2.1.8', true );
      // wp_register_script( 'twitter-bootstrap', '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.0/js/bootstrap.min.js', array( 'jquery' ), '3.0.0', true );
      // wp_register_script( 'knockout', '//cdnjs.cloudflare.com/ajax/libs/knockout/2.3.0/knockout-min.js', array( 'jquery' ), '2.0.0', true );
      // wp_register_script( 'knockout-mapping', '//cdnjs.cloudflare.com/ajax/libs/knockout.mapping/2.3.5/knockout.mapping.min.js', array( 'jquery', 'knockout' ), '2.3.5', true );
      // wp_register_script( 'knockout-bootstrap', '//cdnjs.cloudflare.com/ajax/libs/knockout-bootstrap/0.2.1/knockout-bootstrap.min.js', array( 'jquery', 'knockout', 'bootstrap' ), '0.2.1', true );
      // wp_register_script( 'jquery-lazyload', '//cdnjs.cloudflare.com/ajax/libs/jquery.lazyload/1.8.4/jquery.lazyload.min.js', array( 'jquery' ), '1.8.4', true );
      // wp_register_script( 'jquery-cookie', '//cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.3.1/jquery.cookie.min.js', array( 'jquery' ), '1.3.1', true );
      // wp_register_script( 'jquery-touch-punch', '//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.2/jquery.ui.touch-punch.min.js', array( 'jquery' ), '0.2.2', true );
      // wp_register_script( 'jquery-equalheights', '//cdn.jsdelivr.net/jquery.equalheights/1.3/jquery.equalheights.min.js', array( 'jquery' ), '1.3', true );
      // wp_register_script( 'jquery-placeholder', '//cdn.jsdelivr.net/jquery.placeholder/2.0.7/jquery.placeholder.min.js', array( 'jquery' ), '2.0.7', true );
      // wp_register_script( 'jquery-masonry', '//cdnjs.cloudflare.com/ajax/libs/masonry/3.1.1/masonry.pkgd.js', array( 'jquery' ), '3.1.1', true );
      // wp_register_script( 'jquery-fancybox', '//cdnjs.cloudflare.com/ajax/libs/fancybox/2.0.4/jquery.fancybox.pack.js', array( 'jquery' ), '2.0.4', true );

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
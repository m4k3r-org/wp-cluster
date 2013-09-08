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
        add_action( 'flawless::admin_init', array( __CLASS__, 'handle_upgrade' ) );
      }

    }

    /**
     * Handle upgrading the theme. Only displayed to users who can Update Themes.
     *
     * @since 0.0.2
     */
    static function handle_upgrade() {
      global $wpdb;

      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

      $installed_version = get_option( 'flawless_version' );

      //** If new install. */
      if ( empty( $installed_version ) ) {
        $redirect = add_query_arg( 'admin_splash_screen', 'welcome', Flawless_Admin_URL );
      }

      //** If upgrading from older version */
      if ( version_compare( Flawless_Core_Version, $installed_version, '>' ) ) {
        $redirect = add_query_arg( 'admin_splash_screen', 'updated', Flawless_Admin_URL );
      }

      // @Migrate into module.
      if ( current_theme_supports( 'term-meta' ) && $wpdb->taxonomymeta ) {

        $sql = "CREATE TABLE {$wpdb->taxonomymeta} (
        meta_id bigint(20) unsigned NOT NULL auto_increment,
        taxonomy_id bigint(20) unsigned NOT NULL default '0',
        meta_key varchar(255) default NULL,
        meta_value longtext,
        PRIMARY KEY  (meta_id),
        KEY taxonomy_id (taxonomy_id),
        KEY meta_key (meta_key)
      ) $charset_collate;";

        dbDelta( $sql );

      }

      //** Run the update now in case we have a redirection */
      update_option( 'flawless_version', Flawless_Core_Version );

      if ( $redirect ) {
        die( wp_redirect( $redirect ) );
      }

    }

  }

}
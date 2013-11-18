<?php
/**
 * Domain Mapping
 *
 *
 * @version 0.1.5
 * @module Veneer
 * @author potanin@UD
 */
namespace UsabilityDynamics\Veneer {

  /**
   * Class Locale
   *
   * @module Veneer
   */
  class Mapping {

    /**
     * Current site (blog)
     *
     * @public
     * @static
     * @property $site_id
     * @type {Object}
     */
    public $site_url = null;

    /**
     * Current site's public home URL
     *
     * @public
     * @static
     * @property $home_url
     * @type {Object}
     */
    public $home_url = null;

    /**
     * Current site's administration URL
     *
     * @public
     * @static
     * @property $admin_url
     * @type {Object}
     */
    public $admin_url = null;

    /**
     * Initialize Locale
     *
     * @for Locale
     */
    public function __construct() {

      // URLs
      $this->home_url          = get_home_url();
      $this->site_url          = get_site_url();
      $this->admin_url         = get_admin_url();
      $this->includes_url      = includes_url();
      $this->content_url       = content_url();
      $this->plugins_url       = plugins_url();
      $this->network_site_url  = network_site_url();
      $this->network_home_url  = network_home_url();
      $this->network_admin_url = network_admin_url();
      $this->self_admin_url    = self_admin_url();
      $this->user_admin_url    = user_admin_url();

      // overrite "home" option / home_url()
      add_filter( 'pre_option_home', array( get_class(), 'pre_option_home' ) );

      // Overrite "site" option / site_url()
      add_filter( 'pre_option_siteurl', array( get_class(), 'pre_option_siteurl' ) );

      // Support Vendor paths.
      add_filter( 'plugins_url', array( get_class(), 'plugins_url' ), 50, 3 );

    }

    /**
     * Fix Vendor Plugin Paths
     *
     * @param {String} $url Computed URL, likely wrong for vendor directories.
     * @param $path
     * @param {String} $plugin Path to plugin file that called the plugins_url() method.
     *
     * @return mixed
     */
    public static function plugins_url( $url, $path, $plugin ) {
      global $blog_id;

      if( strpos( $plugin, '/vendor' ) ) {

        $_home_url = untrailingslashit( get_blogaddress_by_id( $blog_id ) );

        // Strip filename
        $plugin = dirname( $plugin );

        $plugin = str_replace( untrailingslashit( WP_BASE_DIR ), $_home_url, $plugin );

        return $plugin;

      }

      return $url;

    }

    /**
     * Fix Site URLs
     *
     * @return string
     */
    public static function pre_option_siteurl() {
      global $blog_id;

      if( Bootstrap::get_instance()->site_id != $blog_id ) {

        if( strpos( get_blogaddress_by_id( $blog_id ), 'http' ) === false ) {
          return ( is_ssl() ? 'https://' : 'http://' ) . untrailingslashit( get_blogaddress_by_id( $blog_id ) ) . ( defined( 'WP_SYSTEM_DIRECTORY' ) ? '/' . WP_SYSTEM_DIRECTORY : '' );
        } else {
          return untrailingslashit( get_blogaddress_by_id( $blog_id ) ) . ( defined( 'WP_SYSTEM_DIRECTORY' ) ? '/' . WP_SYSTEM_DIRECTORY : '' );
        }

      }

      return ( is_ssl() ? 'https://' : 'http://' ) . Bootstrap::get_instance()->domain . ( defined( 'WP_SYSTEM_DIRECTORY' ) ? '/' . WP_SYSTEM_DIRECTORY : '' );

    }

    /**
     * Fix Site frontned URLs
     *
     * @return string
     */
    public static function pre_option_home() {
      global $blog_id;

      if( Bootstrap::get_instance()->site_id != $blog_id ) {

        if( strpos( get_blogaddress_by_id( $blog_id ), 'http' ) === false ) {
          return ( is_ssl() ? 'https://' : 'http://' ) . untrailingslashit( get_blogaddress_by_id( $blog_id ) );
        } else {
          return untrailingslashit( get_blogaddress_by_id( $blog_id ) );
        }

      }

      return ( is_ssl() ? 'https://' : 'http://' ) . Bootstrap::get_instance()->domain;

    }

  }

}


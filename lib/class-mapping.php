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

  if( !class_exists( 'UsabilityDynamics\Veneer\Mapping' ) ) {

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
      static public $site_url = null;

      /**
       * Current site's public home URL
       *
       * @public
       * @static
       * @property $home_url
       * @type {Object}
       */
      static public $home_url = null;

      /**
       * Current site's administration URL
       *
       * @public
       * @static
       * @property $admin_url
       * @type {Object}
       */
      static public $admin_url = null;
      static public $includes_url = null;
      static public $content_url = null;
      static public $plugins_url = null;
      static public $network_site_url = null;
      static public $network_home_url = null;
      static public $network_admin_url = null;
      static public $self_admin_url = null;
      static public $user_admin_url = null;

      /**
       * Initialize Locale
       *
       * @for Locale
       */
      public function __construct() {

        if( !defined( 'WP_BASE_DOMAIN' ) ) {
          wp_die( '<h1>Network Error</h1><p>The WP_BASE_DOMAIN constant is not defined.</p>' );
        }

        // overrite "home" option / home_url()
        add_filter( 'pre_option_home', array( get_class(), 'pre_option_home' ) );

        // Overrite "site" option / site_url()
        add_filter( 'pre_option_siteurl', array( get_class(), 'pre_option_siteurl' ) );

        // Support Vendor paths. Disabled because references get_blogaddress_by_id() too early.
        add_filter( 'plugins_url', array( get_class(), 'plugins_url' ), 50, 3 );

        // URLs
        self::$home_url          = get_home_url();    
        self::$site_url          = get_site_url();
        self::$admin_url         = get_admin_url();       // http://drop.veneer.io/manage/
        self::$includes_url      = includes_url();
        self::$content_url       = content_url();
        self::$plugins_url       = plugins_url();
        self::$network_site_url  = network_site_url();
        self::$network_home_url  = network_home_url();    
        self::$network_admin_url = network_admin_url();   // http://drop.veneer.io/manage/network/
        self::$self_admin_url    = self_admin_url();
        self::$user_admin_url    = user_admin_url();
        
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

        // Being called too early?
        if( !function_exists( 'get_blogaddress_by_id' ) ) {
          wp_die('<h1>Network Error</h1><p>The UsabilityDynamics\Veneer\Mapping:plugin_url() method is called prior to get_blogaddress_by_id() being available.</p>');
        }

        if( strpos( $plugin, '/vendor' ) ) {
        
          if( function_exists( 'get_blogaddress_by_id' ) ) {            
            $_home_url = untrailingslashit( get_blogaddress_by_id( $blog_id ) );
          } else {
            $_home_url = WP_SITEURL;            
          }

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

        if( !function_exists( 'get_blogaddress_by_id' ) ) {
          return 'http://' . WP_BASE_DOMAIN;
        }

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

        if( !function_exists( 'get_blogaddress_by_id' ) ) {
          return 'http://' . WP_BASE_DOMAIN;
        }

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

}


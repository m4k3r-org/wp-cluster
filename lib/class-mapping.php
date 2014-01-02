<?php
/**
 * Domain Mapping
 *
 *
 * @version 0.1.6
 * @module Cluster
 * @author potanin@UD
 */
namespace UsabilityDynamics\Cluster {

  if( !class_exists( 'UsabilityDynamics\Cluster\Mapping' ) ) {

    /**
     * Class Locale
     *
     * @module Cluster
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
        global $cluster;

        if( !$cluster ) {
          wp_die( '<h1>Network Error</h1><p>The $cluster variable is not configured.</p>' );
        }

        if( !defined( 'WP_BASE_DOMAIN' ) ) {
          wp_die( '<h1>Network Error</h1><p>The WP_BASE_DOMAIN constant is not defined.</p>' );
        }

        // overrite "home" option / home_url()
        add_filter( 'pre_option_home', array( get_class(), 'pre_option_home' ) );

        // Overrite "site" option / site_url()
        add_filter( 'pre_option_siteurl', array( get_class(), 'pre_option_siteurl' ) );

        // Support Vendor paths. Disabled because references get_blogaddress_by_id() too early.
        add_filter( 'update_attached_file', array( &$this, 'update_attached_file' ), 50, 2 );
        add_filter( 'get_the_guid', array( &$this, 'get_the_guid' ), 50 );
        add_filter( 'plugins_url', array( &$this, 'plugins_url' ), 50, 3 );
        add_filter( 'content_url', array( &$this, 'replace_network_url' ), 50, 2 );
        add_filter( 'user_admin_url', array( &$this, 'replace_network_url' ), 50, 2 );

        // URLs
        self::$home_url          = get_home_url();
        self::$site_url          = get_site_url();
        self::$admin_url         = get_admin_url();       // http://drop.cluster.io/manage/
        self::$includes_url      = includes_url();
        self::$content_url       = content_url();
        self::$plugins_url       = plugins_url();
        self::$network_site_url  = network_site_url();
        self::$network_home_url  = network_home_url();
        self::$network_admin_url = network_admin_url();   // http://drop.cluster.io/manage/network/
        self::$self_admin_url    = self_admin_url();
        self::$user_admin_url    = user_admin_url();

         /* @todo Move into a test page.
        die( '<pre>' . print_r( array(
          'get_home_url' => get_home_url(),
          'get_site_url' => get_site_url(),
          'get_admin_url' => get_admin_url(),
          'includes_url' => includes_url(),
          'content_url' => content_url(),
          'plugins_url' => plugins_url(),
          'network_site_url' => network_site_url(),
          'network_home_url' => network_home_url(),
          'network_admin_url' => network_admin_url(),
          'self_admin_url' => self_admin_url(),
          'user_admin_url' => user_admin_url(),
          'get_stylesheet_directory_uri' => get_stylesheet_directory_uri(),
          'get_template_directory_uri' => get_template_directory_uri(),
        ), true ) . '</pre>' );
        */

      }

      /**
       * Regenerate GUID
       *
       * For k-boom support, the only thing that uses GUID.
       *
       * @param $file
       * @param $attachment_id
       *
       * @return mixed
       */
      public static function update_attached_file(  $file, $attachment_id ) {
        global $wpdb;

        // Update DB Entry with properly generated GUID.
        $wpdb->update( $wpdb->posts, array( "guid" => get_the_guid( $attachment_id ) ), array( 'ID' => $attachment_id ) );

        return $file;

      }

      /**
       * Remove system path in GUIDs.
       *
       * To fix K-Boom issue, if too esoteric, remove.
       *
       * @param $url
       * @return mixed
       */
      public static function get_the_guid( $url ) {
        global $cluster;

        if( defined( 'WP_SYSTEM_DIRECTORY' ) ) {
          return str_replace( '/' . WP_SYSTEM_DIRECTORY, '', $url );
        }

        return $url;

      }

      public static function replace_network_url( $url, $path ) {
        global $cluster;
        return str_replace( $cluster->network_domain, $cluster->domain, $url );
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
        global $blog_id, $cluster;

        // Fix Vendor Module UTLs.
        if( strpos( $plugin, '/vendor' ) ) {

          // Remove Base Directory Path complete.
          $url = str_replace( trailingslashit( WP_BASE_DIR ), '/', $url );

          // Replace Network URLs with Local URLs.
          $url = str_replace( $cluster->network_domain, $cluster->domain, $url );

          // Replace plugin directory name "e.g. "modules" with nothing
          $url = str_replace( trailingslashit( basename( WP_PLUGIN_DIR ) ), '', $url );

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


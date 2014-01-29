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
        global $wp_cluster;

        if( !$wp_cluster ) {
          wp_die( '<h1>Network Error</h1><p>The $wp_cluster variable is not configured.</p>' );
        }

        if( !defined( 'WP_BASE_DOMAIN' ) ) {
          wp_die( '<h1>Network Error</h1><p>The WP_BASE_DOMAIN constant is not defined.</p>' );
        }

        // add_filter( 'pre_option_home', array( get_class(), 'pre_option_home' ) );
        // add_filter( 'pre_option_siteurl', array( &$this, 'replace_network_url' ) );

        add_filter( 'admin_url', array( &$this, 'admin_url' ), 50, 3 );
        add_filter( 'includes_url', array( &$this, 'includes_url' ), 50, 3 );
        add_filter( 'logout_url', array( &$this, 'logout_url' ), 50, 2 );
        //add_filter( 'content_url', array( &$this, 'admin_url' ), 50, 2 );

        // Replace Network URL with Site URL.
        add_filter( 'content_url', array( &$this, 'replace_network_url' ), 10, 2 );
        add_filter( 'user_admin_url', array( &$this, 'replace_network_url' ), 10, 2 );
        add_filter( 'home_url', array( &$this, 'replace_network_url' ), 10, 2 );
        add_filter( 'site_url', array( &$this, 'replace_network_url' ), 10, 2 );

        // Support Vendor paths. Disabled because references get_blogaddress_by_id() too early.
        add_filter( 'update_attached_file', array( &$this, 'update_attached_file' ), 50, 2 );
        add_filter( 'get_the_guid', array( &$this, 'get_the_guid' ), 50 );
        add_filter( 'plugins_url', array( &$this, 'plugins_url' ), 50, 3 );

        add_filter( 'network_site_url', array( &$this, 'masked_url_fixes' ), 100, 3 );
        add_filter( 'site_url', array( &$this, 'masked_url_fixes' ), 100, 3 );
        add_filter( 'login_redirect', array( &$this, 'masked_url_fixes' ), 100, 3 );

        add_filter( 'lostpassword_url', array( &$this, 'lostpassword_url' ), 100, 3 );
        add_filter( 'admin_url', array( &$this, 'admin_url' ), 100, 3 );
        add_filter( 'includes_url', array( &$this, 'includes_url' ), 100, 3 );
        add_filter( 'home_url', array( &$this, 'home_url' ), 100, 4 );
        add_filter( 'login_url', array( &$this, 'login_url' ), 100, 2 );

        // Special Cases.
        add_filter( 'user_admin_url', array( &$this, 'user_admin_url' ), 100, 2 );
        add_filter( 'network_admin_url', array( &$this, 'network_admin_url' ), 100, 2 );

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

        // die( json_encode( $this->_debug() ) );

      }

      /**
       * Return URL Mapping Array
       *
       * @return array
       */
      private function _debug() {

        return array(
          'wp_login_url' => wp_login_url(),
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
          'get_template_directory_uri' => get_template_directory_uri()
        );

      }

      /**
       * @param $url
       *
       * @return mixed
       */
      public static function user_admin_url( $url ) {
        global $wp_cluster;
        //$url = str_replace( $wp_cluster->network_domain, $wp_cluster->domain, $url );
        //$url = str_replace( $wp_cluster->cluster_domain, $wp_cluster->domain, $url );
        $url = str_replace( '/wp-admin', '/manage', $url );
        return $url;
      }

      /**
       * Causes a redirect loop when trying to use any sites domain instead of "network" domain
       *
       * e.g.
       *  works:        http://usabilitydynamics.com/manage/network/
       *  doesn't:      http://the-denali.dev/manage/network/
       *
       * @param $url
       *
       * @return mixed
       */
      public static function network_admin_url( $url ) {
        global $wp_cluster;
        //$url = str_replace( $wp_cluster->network_domain, $wp_cluster->domain, $url );
        //$url = str_replace( $wp_cluster->cluster_domain, $wp_cluster->domain, $url );
        $url = str_replace( '/wp-admin/network/', '/manage/network', $url );
        return $url;
      }

      /**
       * Must Match manage/login exactly without trailing slash.
       *
       * @param $url
       *
       * @return mixed
       */
      public static function logout_url( $url ) {
        $url = str_replace( 'wp-login.php', 'manage/login', $url );
        return $url;
      }

      /**
       * http://usabilitydynamics.com/wp-login.php -> http://usabilitydynamics.com/manage/login/
       * @param $url
       * @param $redirect
       *
       * @internal param $login_url
       * @return mixed
       */
      public static function login_url( $url, $redirect ) {

        //$url = str_replace( '/vendor/wordpress/core', '', $url );
        $url = str_replace( 'wp-login.php', 'manage/login', $url );

        return $url;
      }

      /**
       *
       * http://usabilitydynamics.com/vendor/wordpress/core -> http://usabilitydynamics.com
       * @param $url
       * @param $path
       * @param $orig_scheme
       * @param $blog_id
       *
       * @return mixed
       */
      public static function home_url( $url, $path, $orig_scheme, $blog_id ) {
        $url = str_replace( '/vendor/wordpress/core', '', $url );
        return $url;
      }

      /**
       * Includes URL
       *
       * http://sugarsociety.com/wp-includes -> http://sugarsociety.com/includes
       * @param $url
       * @return mixed
       */
      public static function includes_url( $url ) {
        global $wp_cluster;

        $url = str_replace( $wp_cluster->cluster_domain, $wp_cluster->domain, $url );
        $url = str_replace( 'wp-includes', 'includes', $url );
        return $url;
      }

      /**
       * Content URL
       *
       * http://edm.network.stuff.com -> http://sugarsociety.com
       *
       * @param $url
       *
       * @return mixed
       */
      public static function content_url( $url ) {
        global $wp_cluster;

        $url = str_replace( $wp_cluster->cluster_domain, $wp_cluster->domain, $url );
        return $url;

      }

      /**
       * Fixes Various Masked URLs
       *
       * Fixes redirect_to hidden value on login forms.
       *
       * @todo There is a redirection issue after password reset where forwarded to manage/wp-login.php which is fixed via .htaccess 301 redirection.
       * @todo Password reset e-mails use http://domain.com/wp-login.php?action=rp&key=XW9Bzard301lWKjNOTRE&login=andypotanin reset URLs.
       * @todo registration_redirect filter
       *
       * @param $url
       *
       * @return string
       */
      public static function masked_url_fixes( $url ) {

        // Conceal WordPress Location.
        if( strpos( $url, '/vendor/wordpress/core' ) ) {
          $url = str_replace( '/vendor/wordpress/core', '', $url );
        }

        if( strpos( $url, '/wp-admin' ) ) {
          $url = str_replace( '/wp-admin', '/manage', $url );
        }

        if( strpos( $url, '/wp-login.php' ) ) {
          $url = str_replace( '/wp-login.php', '/manage/login', $url );
        }

        return $url;

      }

      /**
       * Site Admin URL
       *
       * get_admin_url && self_admin_url
       *
       * http://sugarsociety.com/wp-admin/ -> http://sugarsociety.com/manage
       *
       * @param $url
       * @param $path
       * @param $blog_id
       *
       * @return mixed
       */
      public static function admin_url( $url, $path, $blog_id ) {

        $url = str_replace( '/wp-admin/', '/manage/', $url );

        // @todo replace with api.site.com
        // $url = str_replace( 'wp-ajax.php', '/manage/', $url );

        return $url;

      }

      /**
       * @param $url
       * @param $redirect
       *
       * @return mixed
       */
      public static function lostpassword_url( $url, $redirect ) {

        $url = str_replace( 'wp-login.php', 'manage/login', $url );

        // @todo replace with api.site.com
        // $url = str_replace( 'wp-ajax.php', '/manage/', $url );

        return $url;

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

        if( defined( 'WP_SYSTEM_DIRECTORY' ) ) {
          return str_replace( '/' . WP_SYSTEM_DIRECTORY, '', $url );
        }

        return $url;

      }

      /**
       * Network URL
       *
       * @param $url
       *
       * @return mixed
       */
      public static function replace_network_url( $url ) {
        global $wp_cluster;
        $url = str_replace( $wp_cluster->cluster_domain, $wp_cluster->network_domain, $url );
        return str_replace( $wp_cluster->network_domain, $wp_cluster->domain, $url );
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
        global $wp_cluster;

        $url = str_replace( $wp_cluster->cluster_domain, $wp_cluster->domain, $url );

        // Fix Vendor Module UTLs.
        if( strpos( $plugin, '/vendor' ) ) {

          @$url = ( is_ssl() ? 'https://' : 'http://' ) . ( $wp_cluster->domain  . '' . end( explode( $wp_cluster->domain, $url ) ) );

          // Remove Base Directory Path complete.
          // $url = str_replace( trailingslashit( WP_BASE_DIR ), '/', $url );

          // Replace Network URLs with Local URLs.
          // $url = str_replace( $wp_cluster->network_domain, $wp_cluster->domain, $url );

          // Replace plugin directory name "e.g. "modules" with nothing
          // $url = str_replace( trailingslashit( basename( WP_PLUGIN_DIR ) ), '', $url );

        }

        return $url;

      }

      /**
       * Fix Site URLs
       *
       * @return string
       */
      public static function pre_option_siteurl() {
        global $wp_cluster, $blog_id;

        if( $wp_cluster->site_id != $blog_id ) {

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


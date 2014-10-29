<?php
/**
 * URL Rewrites
 *
 * @version 0.1.6
 * @module Veneer
 * @author potanin@UD
 */
namespace UsabilityDynamics\Veneer {

  if( !class_exists( 'UsabilityDynamics\Veneer\Rewrites' ) ) {

    /**
     * Class Rewrites
     *
     * @property array $urls Holds the debug info for all of our URLs
     * @module Veneer
     */
    class Rewrites {

      /**
       * Initialize Locale
       *
       * @for Locale
       */
      public function __construct() {
        global $wp_veneer, $current_blog;

        if( !$wp_veneer ) {
          wp_die( '<h1>Veneer Error</h1><p>The $wp_veneer variable is not configured.</p>' );
        }

	      // Don't apply anything if MS is not installed at the moment.
	      if( isset( $current_blog ) && isset( $current_blog->_default ) ) {
		      return;
	      }

	      if( !defined( 'WP_BASE_DOMAIN' ) ) {

          if( defined( 'WP_HOME' ) ) {
            define( 'WP_BASE_DOMAIN', str_replace( array( 'https://', 'http://' ), '', WP_HOME ) );
          }

          if( !defined( 'WP_BASE_DOMAIN' ) ) {
            wp_die( '<h1>Veneer Error</h1><p>The WP_BASE_DOMAIN constant is not defined.</p>' );
          }

        }

        // Replace Network URL with Site URL.
        add_filter( 'wp_redirect', array( $this, 'wp_redirect' ), 10, 2 );
        add_filter( 'pre_option_home', array( $this, '_option_home' ), 10 );
        add_filter( 'content_url', array( $this, 'replace_network_url' ), 10, 2 );
        add_filter( 'site_url', array( $this, 'replace_network_url' ), 10, 3 );
        add_filter( 'plugins_url', array( $this, 'replace_network_url' ), 10, 2 );

        // Support Vendor paths. Disabled because references get_blogaddress_by_id() too early.
        add_filter( 'update_attached_file', array( $this, 'update_attached_file' ), 50, 2 );
        add_filter( 'get_the_guid', array( $this, 'get_the_guid' ), 50 );
        add_filter( 'plugins_url', array( $this, 'plugins_url' ), 50, 3 );

        add_filter( 'network_site_url', array( $this, 'masked_url_fixes' ), 100, 3 );
        add_filter( 'site_url', array( $this, 'masked_url_fixes' ), 100, 3 );
        add_filter( 'login_redirect', array( $this, 'masked_url_fixes' ), 100, 3 );

        add_filter( 'lostpassword_url', array( $this, 'lostpassword_url' ), 100, 3 );
        add_filter( 'includes_url', array( $this, 'includes_url' ), 100, 3 );
        add_filter( 'home_url', array( $this, 'home_url' ), 100, 4 );
        add_filter( 'login_url', array( $this, 'login_url' ), 100, 2 );
        add_filter( 'logout_url', array( $this, 'logout_url' ), 50, 2 );

        // Special Cases.
	      add_filter( 'admin_url', array( $this, 'admin_url' ), 100, 3 );
	      add_filter( 'user_admin_url', array( $this, 'user_admin_url' ), 100, 2 );
	      add_filter( 'network_admin_url', array( $this, 'network_admin_url' ), 100, 2 );
	      add_filter( 'user_admin_url', array( $this, 'replace_network_url' ), 10, 2 );

        add_filter( 'stylesheet_directory_uri', array( $this, 'stylesheet_directory_uri' ), 100, 3 );
        add_filter( 'template_directory_uri', array( $this, 'template_directory_uri' ), 100, 3 );
        add_filter( 'theme_root_uri', array( $this, 'theme_root_uri' ), 100, 3 );

        // Carrington Build
        add_filter( 'cfct-build-module-urls', array( $this, 'cfct_build_module_urls' ), 100, 3 );
        add_filter( 'cfct-build-module-url', array( $this, 'replace_network_url' ), 10, 3 );

        // die( json_encode( $this->_debug() ) );
        // add_action( 'template_redirect', function() { global $wp_veneer; wp_send_json_success( $wp_veneer->_rewrites ); });

      }

      /**
       * Prepent Protocol Prefix to a URL.
       *
       * @param $url
       *
       * @method prepend_scheme
       * @since 0.6.2
       * @return string
       */
      public static function prepend_scheme( $url = '' ) {

        $url = str_replace( array( 'https://', 'http://', '//' ), '', $url );

        if( is_ssl() ) {
          return 'https://' . $url;
        }

        return 'http://' . $url;

      }

      /**
       * Refreshes our URLs
       */
      public function _refresh_urls(){

        /** Setup our array */
        $this->urls = array(
          'relative:admin-ajax' => admin_url( 'admin-ajax.php', 'relative' ),
          'home_url' => get_home_url(),
          'login_url' => wp_login_url(),
          'site_url' => get_site_url(),
          'admin_url' => get_admin_url(),
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
       *
       * @param $location
       * @param $status
       *
       * @return mixed
       */
      public function wp_redirect( $location, $status ) {
        return $location;
      }

      /**
       * Simgple Home URL Detection.
       *
       * Hooks into get_option( 'home' )
       *
       * @param $_always_false
       * @return string
       */
      public function _option_home( $_always_false ) {
        global $wp_veneer;

	      //die('$default' . $_always_false );

        if( defined( 'WP_HOME' ) ) {
          // $default = str_replace( WP_HOME, $default, $default );
        }

	      //die( '<pre>' . print_r( $wp_veneer, true ) . '</pre>');

	      if( $wp_veneer->site ) {
		      return ( $wp_veneer->site ? 'http://' . $wp_veneer->site : '' );
	      }

        return $_always_false;

      }

      /**
       * Fix DDP problem
       */
      public function template_directory_uri( $template_dir_uri, $template, $theme_root_uri ) {

        $template_dir_uri = preg_replace( '~(^|[^:])//+~', '\\1/', str_replace( '\\', '/', $template_dir_uri ) );
        $theme_root_uri   = preg_replace( '~(^|[^:])//+~', '\\1/', str_replace( '\\', '/', $theme_root_uri ) );

        if( strpos( $template_dir_uri, get_home_url() ) === 0 ) {
          return $template_dir_uri;
        }

        if( strpos( $template_dir_uri, 'http' ) === 0 ) {
          return $template_dir_uri;
        }

        return untrailingslashit( get_home_url() ) . '/' . $template_dir_uri;
      }

      /**
       * Fixing issue where theme resides outside any known directories
       */
      public function theme_root_uri( $uri, $siteurl, $tofind ){

        /** If we don't have the custom location defined, bail */
        if( !defined( 'WP_THEME_STORAGE_DIR' ) ){
          return $uri;
        }

        /** If the URI is actually a valid URL, bail */
        if( filter_var( $uri, FILTER_VALIDATE_URL ) === true ){
          return $uri;
        }
        /** If the URL has the base directory */
        if( defined( 'WP_BASE_DIR' ) && stripos( $uri, WP_BASE_DIR ) !== false ){
          $uri = rtrim( $siteurl, '/' ) . '/' . trim( str_ireplace( WP_BASE_DIR, '', $uri ), '/' );
        }
        /** Return default */
        return $uri;
      }

      /**
       * Fix carrington build stuff
       */
      function cfct_build_module_urls( $urls ){
        foreach( $urls as &$url ){
          if( defined( 'WP_BASE_DIR' ) && stripos( $url, WP_BASE_DIR ) === 0 ){
            $url = str_ireplace( WP_BASE_DIR, WP_BASE_URL, $url );
          }
        }
        return $urls;
      }

      /**
       * Fix DDP problem
       */
      public function stylesheet_directory_uri( $stylesheet_dir_uri, $stylesheet, $theme_root_uri ) {

        $stylesheet_dir_uri = preg_replace( '~(^|[^:])//+~', '\\1/', str_replace( '\\', '/', $stylesheet_dir_uri ) );
        $theme_root_uri   = preg_replace( '~(^|[^:])//+~', '\\1/', str_replace( '\\', '/', $theme_root_uri ) );

        if( strpos( $stylesheet_dir_uri, get_home_url() ) === 0 ) {
          return $stylesheet_dir_uri;
        }

        if( strpos( $stylesheet_dir_uri, 'http' ) === 0 ) {
          return $stylesheet_dir_uri;
        }


        return untrailingslashit( get_home_url() ) . '/' . $stylesheet_dir_uri;

      }

      /**
       * Return URL Rewrites Array
       *
       * @return array
       */
      private function _debug() {
        /** Refresh our URLs, first */
        $this->_refresh_urls();
        return $this->urls;
      }

      /**
       * @param $url
       *
       * @return mixed
       */
      public static function user_admin_url( $url ) {
        global $wp_veneer;

	      if( $wp_veneer->get( 'rewrites.manage' ) ) {
		      $url = str_replace( '/wp-admin', '/manage', $url );
	      }

        if( strpos( $url, 'http' ) !== 0 ) {
          die($url);
        }

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
        global $wp_veneer;

	      if( $wp_veneer->get( 'rewrites.manage' ) ) {
		      $url = str_replace( '/wp-admin/network/', '/manage/network', $url );
	      }

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
	      global $wp_veneer;

	      if( $wp_veneer->get( 'rewrites.manage' ) ) {
		      $url = str_replace( '/wp-login.php', '/manage/login', $url );
	      }

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
	      global $wp_veneer;

	      if( $wp_veneer->get( 'rewrites.login' ) ) {
		      $url = str_replace( '/wp-login.php', '/manage/login', $url );
	      }

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
        $url = str_replace( '/vendor/libraries/automattic/wordpress', '', $url );
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
        $url = str_ireplace( 'wp-includes/js', 'assets/scripts', $url );
        $url = str_ireplace( 'wp-includes/css', 'assets/styles', $url );
        $url = str_ireplace( 'wp-includes/images', 'assets/images', $url );
        $url = str_ireplace( 'wp-includes/', 'assets/', $url );
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
        global $wp_veneer;

        $url = str_replace( $wp_veneer->network, $wp_veneer->site, $url );

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
	      global $wp_veneer;

        // Conceal WordPress Location. (new)
        if( strpos( $url, '/vendor/libraries/automattic/wordpress' ) ) {
          $url = str_replace( '/vendor/libraries/automattic/wordpress', '', $url );
        }

        // Conceal WordPress location. (legacy)
        if( strpos( $url, '/vendor/wordpress/core' ) ) {
          $url = str_replace( '/vendor/wordpress/core', '', $url );
        }

        if( strpos( $url, '/wp-admin' ) && $wp_veneer->get( 'rewrites.manage' ) ) {
          $url = str_replace( '/wp-admin', '/manage', $url );
        }

	      if( strpos( $url, '/wp-login.php' ) && $wp_veneer->get( 'rewrites.login' ) ) {
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
	      global $wp_veneer;

	      if( $wp_veneer->get( 'rewrites.manage' ) ) {
	        $url = str_replace( '/vendor/libraries/automattic/wordpress/manage/', '/manage/', $url );
	        $url = str_replace( '/wp-admin/', '/manage/', $url );
	      }

        // $url = str_replace( '/wp-admin/', '/manage/', $url );

        // @note Got to be careful here since admin cookies only apply to /manage. if( defined( 'ADMIN_COOKIE_PATH' ) && ADMIN_COOKIE_PATH ) {}
        // $url = str_replace( '/admin-ajax.php', '/api', $url );

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
	      global $wp_veneer;

	      if( $wp_veneer->get( 'rewrites.login' ) ) {
		      $url = str_replace( 'wp-login.php', 'manage/login', $url );
	      }

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
       * @param      $url
       * @param null $path
       * @param null $schema
       *
       * @return mixed
       */
      public static function replace_network_url( $url, $path = null, $schema = null ) {
        global $wp_veneer;

        $_home_url = defined( 'WP_HOME' ) ? WP_HOME : ( defined( 'WP_SITE_URL' ) ? WP_SITE_URL : '' );

        // Relative URLs are opoosite, we actually try to strip our URL.
        if( $schema === 'relative' && ( strpos( $url, $_home_url ) === 0 ) ) {
          return str_replace( $_home_url, '', $url );
        }

        if( $schema !== 'relative' && $_home_url ) {
          $url = str_replace( $_home_url, $wp_veneer->site, $url );
        }

        if( $schema !== 'relative' ) {
          $url = str_replace( $_home_url, $wp_veneer->site, $url );
        }

        $url =  $wp_veneer->network && $wp_veneer->site ? str_replace( $wp_veneer->network, $wp_veneer->site, $url ) : $url;

        if( $schema !== 'relative' ) {
          $url = self::prepend_scheme( $url );
        }

        return $url;
      }

      /**
       * Fix Vendor Plugin Paths
       *
       * Docs from WP Codex:
       * Retrieves the absolute URL to the plugins directory (without the trailing slash) or, when using the $path
       * argument, to a specific file under that directory. You can either specify the $path argument as a hardcoded
       * path relative to the plugins directory, or conveniently pass __FILE__ as the second argument to make the $path
       * relative to the parent directory of the current PHP script file.
       *
       * @param {String} $url Computed URL, likely wrong for vendor directories.
       * @param {String} $path Path to the plugin file of which URL you want to retrieve, relative to the plugins directory or to $plugin if specified.
       * @param {String} $plugin Path under the plugins directory of which parent directory you want the $path to be relative to.
       *
       * @return mixed
       */
      public static function plugins_url( $url, $path, $plugin ) {
        global $wp_veneer;

        $path = wp_normalize_path( $path );

        /** Strip filename and get just the path */
        if( strpos( $plugin, '.php' ) ) {
          $plugin = wp_normalize_path( dirname( $plugin ) );
        }

        /** First, if we have $plugin and $path defined, we use both */
        if( $path && $plugin && defined( 'WP_BASE_DIR' ) ){
          $url = str_ireplace( wp_normalize_path( WP_BASE_DIR ), '', $plugin );
          $url = rtrim( site_url( $url ), '/' ) . '/' . ltrim( $path, '/' );
        }

        /** Now, if we just have the path, then use that only */
        if( $path && !$plugin && defined( 'WP_BASE_DIR' ) ) {
          $url = str_ireplace( wp_normalize_path( WP_BASE_DIR ), '', wp_normalize_path( WP_PLUGIN_DIR ) );
          $url = rtrim( site_url( $url ), '/' ) . '/' . ltrim( $path, '/' );
        }

        /** Finally, if we don't have $path but have $plugin, then maybe fix url to plugin. peshkov@UD */
        if( !$path && $plugin ) {
          if( strpos( $url, basename( $plugin ) ) === false ) {
            $url = rtrim( $url, '/' ) . '/' . basename( $plugin );
          }
        }

        /**
         * HACKY Fix because composer doesn't install directories with underscores, the second one is just lazy.
         */
        switch( true ){
          case stripos( $url, 'modules/simple_email_subscriber' ) !== false:
            $url = str_ireplace( 'modules/simple_email_subscriber', 'modules/simple-email-subscriber', $url );
          break;
          case stripos( $plugin, 'sitepress-multilingual-cms' ) !== false:
            $url = str_ireplace( 'sitepress-multilingual-cms/inc/installer/includes', 'sitepress-multilingual-cms/inc/installer', $url );
          break;
        }

        /** Ensure a valid site name */
        $url = str_replace( array( $wp_veneer->network ), array( $wp_veneer->site ), $url );

        /** Fix for Win system */
        $url = str_replace( '\\', '/', $url );

        return $url;

      }

    }

  }

}


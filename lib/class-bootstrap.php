<?php
/**
 * UsabilityDynamics\Veneer Bootstrap
 *
 * @verison 0.3.0
 * @author potanin@UD
 * @namespace UsabilityDynamics\Veneer
 */
namespace UsabilityDynamics\Veneer {

  if( !class_exists( 'UsabilityDynamics\Veneer\Bootstrap' ) ) {

    /**
     * Bootstrap Veneer
     *
     * @class Bootstrap
     * @author potanin@UD
     * @version 0.0.1
     */
    class Bootstrap {

      /**
       * Veneer core version.
       *
       * @static
       * @property $version
       * @type {Object}
       */
      public static $version = '0.3.0';

      /**
       * Textdomain String
       *
       * @public
       * @property text_domain
       * @var string
       */
      public static $text_domain = 'veneer';

      /**
       * Singleton Instance Reference.
       *
       * @public
       * @static
       * @property $instance
       * @type {Object}
       */
      public static $instance = false;

      /**
       * Current Network ID
       *
       * @public
       * @static
       * @property $network_id
       * @type {Object}
       */
      public $network_id = null;

      /**
       * Current site (blog)
       *
       * @public
       * @static
       * @property $site_id
       * @type {Object}
       */
      public $site_id = null;

      /**
       * The literal domain of the current request.
       *
       * @public
       * @static
       * @property $site_id
       * @type {Object}
       */
      public $requested_domain = null;

      /**
       * The main mapped domain of a site.
       *
       * @public
       * @static
       * @property $domain
       * @type {Object}
       */
      public $domain = null;

      /**
       * The domain name of the network this site belongs to.
       *
       * @public
       * @static
       * @property $domain
       * @type {Object}
       */
      public $network_domain = null;

      /**
       * Current theme using 
       *
       * @public
       * @static
       * @property $domain
       * @type {Object}
       */
      public $theme = null;

      /**
       * Constructor.
       *
       * UsabilityDynamics components should be avialable.
       * - class_exists( '\UsabilityDynamics\API' );
       * - class_exists( '\UsabilityDynamics\Utility' );
       *
       * @for Loader
       * @method __construct
       */
      public function __construct() {
        global $wpdb, $current_site, $current_blog, $veneer;

        // Return singleton instance
        if( self::$instance ) {
          return self::$instance;
        }

        if( !defined( 'MULTISITE' ) ) {
          wp_die( '<h1>Veneer Fatal Error.</h1><p>MULTISITE constant is not defined.</p>' );
        }

        if( !defined( 'UPLOADBLOGSDIR' ) ) {
          wp_die( '<h1>Veneer Fatal Error.</h1><p>UPLOADBLOGSDIR constant is not defined.</p>' );
        }

        // Seek ./vendor/autoload.php and autoload
        if( is_file( basename( __DIR__ ) . DIRECTORY_SEPARATOR . 'vendor/autoload.php' ) ) {
          include_once( basename( __DIR__ ) . DIRECTORY_SEPARATOR . 'vendor/autoload.php' );
        }

        // Save context reference.
        $veneer = self::$instance = & $this;

        // Identify site being requested. This should be handled by sunrise.php.
        if( !$current_site || !$current_blog ) {
          $this->identify_site();
        }

        if( !$current_site ) {
          wp_die( 'Site not identified.' );
        }

        // Current site.
        $this->organization     = $current_site->site_name;
        $this->site_id          = $wpdb->blogid;
        $this->network_id       = $wpdb->siteid;
        $this->requested_domain = $current_blog->domain;
        $this->domain           = $wpdb->get_var( "SELECT domain FROM {$wpdb->blogs} WHERE blog_id = '{$wpdb->blogid}' LIMIT 1" );
        $this->network_domain   = $wpdb->get_var( "SELECT domain FROM {$wpdb->site} WHERE id = {$this->network_id}" );
        $this->allowed_domains  = array( $this->domain );
        $this->is_valid         = in_array( $this->requested_domain, $this->allowed_domains ) ? true : false;
        $this->is_public        = $current_blog->public;
        $this->is_main_site     = is_main_site();
        $this->is_multisite     = is_multisite();
        $this->is_main_network  = is_main_network();

        if( !$this->is_valid ) {
          wp_die( '<h1>Veneer Network Error</h1><p>Your request is for an invalid domain.</p>' );
        }

        // Fix MultiSite URLs
        $this->fix_urls();

        // Initialize Controllers and Helpers
        $this->developer = new Developer();
        $this->settings  = new Settings();
        $this->media     = new Media();
        $this->mapping   = new Mapping();
        $this->api       = new API();
        $this->theme     = wp_get_theme();

        // Must set or long will not work
        if( !defined( 'COOKIE_DOMAIN' ) ) {
          define( 'COOKIE_DOMAIN', $this->requested_domain );
        }

        // Initialize all else.
        add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
        add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 21 );
        add_action( 'wp_before_admin_bar_render', array( $this, 'veneer_toolbar' ), 10 );
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ), 500 );
        add_action( 'template_redirect', array( $this, 'template_redirect' ) );

        // Add Veneer Scripts & Styles.
        add_action( 'admin_enqueue_scripts', array( get_class(), 'admin_enqueue_scripts' ), 20 );

        // Modify Core UI.
        add_filter( 'wpmu_blogs_columns', array( $this, 'wpmu_blogs_columns' ) );
        add_action( 'manage_sites_custom_column', array( $this, 'manage_sites_custom_column' ), 10, 2 );

        if( is_network_admin() ) {
          add_action( 'network_admin_menu', array( &$this, 'network_admin_menu' ), 100 );
        }

        // @chainable. (Node.js habbit)
        return $this;

      }

      /**
       * Added by anton.
       */
      public function init() {
        // global $wpi_xml_server;
        // $wpi_xml_server = new \UsabilityDynamics\UD_XMLRPC( 'https://raas.usabilitydynamics.com:443', \UsabilityDynamics\get_option('ud_api_public_key'), 'WordPress/3.7.1', 'ud' );
      }

      public function admin_enqueue_scripts() {
        wp_enqueue_style( 'veneer-app', home_url( '/vendor/usabilitydynamics/wp-veneer/styles/app.css' ), array(), self::$version );
      }

      /**
       * Add "ID" and "Thumbnail" columns to Network Sites table.
       *
       * @todo Use register_column_headers();
       *
       * @param $sites_columns
       * @return mixed
       */
      public function wpmu_blogs_columns( $sites_columns ) {

        // Insert ID at position.
        $sites_columns = array_merge( array_slice( $sites_columns, 0, 1 ), array(
          'blog_id' => __( 'ID', self::$text_domain ),
        ), array_slice( $sites_columns, 1 ) );

        // Insert Thumbnail at position.
        $sites_columns = array_merge( array_slice( $sites_columns, 0, 10 ), array(
          'thumbnail' => __( 'Thumbnail', self::$text_domain )
        ), array_slice( $sites_columns, 10 ) );

        return $sites_columns;

      }

      /**
       * Dispaly "ID" and "Thumbnail" cells on Network Sites table.
       *
       * @param $column_name
       * @param $blog_id
       */
      public function manage_sites_custom_column( $column_name, $blog_id ) {

        switch ($column_name) {

          case 'blog_id':
            echo '<p>' . $blog_id . '</p>';
          break;

          case 'thumbnail':
            echo '<img src="" class="veneer-site-thumbnail"/>';
          break;

        }

      }

      /**
       * Site Administration Menus.
       *
       * @method admin_menu
       */
      public function admin_menu() {

        // Add Site Administration (Settings -> Veneer).
        add_submenu_page( 'options-general.php', __( 'Veneer', self::$text_domain ), __( 'Veneer', self::$text_domain ), 'manage_network', 'veneer', function() {
          include( dirname( __DIR__ ) . '/views/site-settings.php' );
        });

      }

      /**
       * Network Administration Menu.
       *
       */
      public function network_admin_menu() {

        // Only admin can see W3TC notices and errors
        // add_action('admin_notices', array( &$this, 'admin_notices' ));
        // add_action('network_admin_notices', array( &$this, 'admin_notices' ));

        // Add Network Administration.
        add_menu_page( __( 'Veneer', self::$text_domain ), __( 'Veneer', self::$text_domain ), 'manage_network', 'veneer', function() {

          // die( network_admin_url( 'veneer-icon.png' ) );

          include( dirname( __DIR__ ) . '/views/network-settings.php' );

        });

      }

      /**
       * Identify Request
       *
       * Currenty not used and handled by sunrise.php
       *
       * @method identify_site
       */
      public function identify_site() {
        global $site_id, $blog_id, $wpdb, $current_blog, $current_site;

        $_host = $_SERVER[ 'HTTP_HOST' ];

        $_lookup = $wpdb->get_row( "SELECT * FROM {$wpdb->blogs} WHERE domain = '{$_host}' LIMIT 1" );

        if( $_lookup ) {
          $blog_id               = $wpdb->blogid = $_lookup->blog_id;
          $site_id               = $wpdb->siteid = $_lookup->site_id;
          $current_site          = $wpdb->get_row( "SELECT * from {$wpdb->site} WHERE id = '{$site_id}' LIMIT 0,1" );
          $current_site->blog_id = $blog_id;
        }

      }

      /**
       * Initializer.
       *
       * @method plugins_loaded
       * @author potanin@UD
       */
      public function plugins_loaded() {

        // add_action( 'shutdown', array( $this, 'shutdown' ) );
        // add_action( 'init', array( $this, 'init' ) );
        // add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
        // add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
        // add_action( 'wp_loaded', array( $this, 'wp_loaded' ) );

      }

      /**
       * Add Frontend Headers
       *
       */
      public function template_redirect() {

        if( !headers_sent() ) {
          header( 'Server: Veneer' );
          header( 'X-Powered-By: Veneer ' . Bootstrap::$version );
          header( 'X-Veneer-Version:' . Bootstrap::$version );
        }

      }

      /**
       * Initialize Admin
       *
       * @method admin_init
       * @author potanin@UD
       */
      public function admin_init() {

        /* Remove Akismet API Key Nag */
        remove_action( 'admin_notices', 'akismet_warning' );

        /* Disable BuddyPress Nag */
        remove_action( 'admin_notices', 'bp_core_update_nag', 5 );
        remove_action( 'network_admin_notices', 'bp_core_update_nag', 5 );

      }

      /**
       * Update Amin Menu
       *
       * @method admin_bar_menu
       * @author potanin@UD
       */
      public function admin_bar_menu( $wp_admin_bar = false ) {

        if( !is_super_admin() || !is_multisite() || !$wp_admin_bar ) {
          return;
        }

        $wp_admin_bar->add_menu( array(
          'parent' => 'network-admin',
          'id'     => 'network-settings',
          'title'  => __( 'Settings', self::$text_domain ),
          'href'   => network_admin_url( 'settings.php' ),
        ) );

      }

      /**
       * Add Veneer Toolbar
       *
       * @method veneer_toolbar
       * @for Boostrap
       */
      public function veneer_toolbar() {
        global $wp_admin_bar, $veneer;

        $wp_admin_bar->add_menu( array(
            'id'   => 'veneer',
            'meta'   => array(
              'html'     => '<div class="veneer-toolbar-info"></div>',
              'target'   => '',
              'onclick'  => '',
              'title'    => 'Veneer',
              'tabindex' => 10,
              'class' => 'veneer-toolbar'
            ),
            'title' => 'Veneer',
            'href' => network_admin_url( 'admin.php?page=veneer' )
          )
        );

        $wp_admin_bar->add_menu( array(
          'parent' => 'veneer',
          'id'   => 'veneer-cdn',
          'meta' => array(),
          'title' => 'CDN',
          'href' => network_admin_url( 'admin.php?page=veneer#panel=cdn' )
        ));

        $wp_admin_bar->add_menu( array(
          'parent' => 'veneer',
          'id'   => 'veneer-search',
          'meta' => array(),
          'title' => 'Search',
          'href' => network_admin_url( 'admin.php?page=veneer#panel=search' )
        ));

        $wp_admin_bar->add_menu( array(
          'parent' => 'veneer',
          'id'   => 'veneer-varnish',
          'meta' => array(),
          'title' => 'Varnish',
          'href' => network_admin_url( 'admin.php?page=veneer#panel=varnish' )
        ));

        $wp_admin_bar->add_menu( array(
          'parent' => 'veneer',
          'id'   => 'veneer-jobs',
          'meta' => array(),
          'title' => 'Jobs',
          'href' => network_admin_url( 'admin.php?page=veneer#panel=jobs' )
        ));

      }

      /**
       * Automatically fix MS URLs that get messed up
       *
       * UPLOADBLOGSDIR must be set in wp-config.php to take affect, UPLOADS is defined based on site's ID
       * This would be the place to overwrite the media/{ID}/files to something else.
       *
       * network_site_url - http://network.nightculture.loc/wp-admin/network/ -> http://network.nightculture.loc/system/wp-admin/network/
       *
       */
      public function fix_urls() {

        if( defined( 'WP_VENEER_DOMAIN_MEDIA' ) && WP_VENEER_DOMAIN_MEDIA && !defined( 'BLOGUPLOADDIR' ) ) {
          define( 'BLOGUPLOADDIR', WP_BASE_DIR . '/' . UPLOADBLOGSDIR . '/' . Bootstrap::get_instance()->domain );
        } else {
          define( 'BLOGUPLOADDIR', WP_BASE_DIR . '/' . UPLOADBLOGSDIR . '/' . Bootstrap::get_instance()->site_id );
        }

        // Add handling for /manage
        add_filter( 'network_site_url', array( get_class(), 'network_site_url' ) );
        add_filter( 'network_admin_url', array( get_class(), 'network_site_url' ) );

        // Add handling for /manage
        add_filter( 'admin_url', array( get_class(), 'admin_url' ) );

      }

      /**
       * Network URL
       *
       *
       * @author potanin@UD
       * @method network_site_url
       *
       * @param $url
       *
       * @return mixed
       */
      public static function network_site_url( $url ) {
        return str_replace( '/wp-admin', '/manage', $url );
      }

      /**
       * Manage URL
       *
       *
       * @author potanin@UD
       * @method network_site_url
       *
       * @param $url
       *
       * @return mixed
       */
      public static function admin_url( $url ) {

        if( defined( 'WP_SYSTEM_DIRECTORY' ) ) {
          return str_replace( '/system/wp-admin', '/manage', $url );
        } else {
          return str_replace( '/wp-admin', '/manage', $url );
        }

      }

      /**
       * Error Handler
       *
       * @param $errno
       * @param $errstr
       * @param $errfile
       * @param $errline
       *
       * @param $errfile
       *
       * @return bool
       */
      public static function error_handler( $errno = null, $errstr = '', $errfile = null, $errline = null ) {

        wp_die( 'Veneer error' );

        // This error code is not included in error_reporting
        if( !( error_reporting() & $errno ) ) {
          return;
        }

        switch( $errno ) {

          // Fatal
          case E_ERROR:
          case E_CORE_ERROR:
          case E_RECOVERABLE_ERROR:
          case E_COMPILE_ERROR:
          case E_USER_ERROR:
            wp_die( "<h1>Website Temporarily Unavailable</h1><p>We apologize for the inconvenience and will return shortly.</p>" );
            break;

          // Do Nothing
          case E_WARNING:
          case E_USER_NOTICE:
            return true;
            break;

          // No Idea.
          default:
            return;
            // wp_die( "<h1>Website Temporarily Unavailable</h1><p>We apologize for the inconvenience and will return shortly.</p>" );
            break;
        }

        return true;

      }

      /**
       * Get Setting.
       *
       *    // Get Setting
       *    Veneer::get( 'my_key' )
       *
       * @method get
       *
       * @for Flawless
       * @author potanin@UD
       * @since 0.1.1
       */
      public static function get( $key, $default = null ) {
        return self::$instance->_settings ? self::$instance->_settings->get( $key, $default ) : null;
      }

      /**
       * Set Setting.
       *
       * @usage
       *
       *    // Set Setting
       *    Veneer::set( 'my_key', 'my-value' )
       *
       * @method get
       * @for Flawless
       *
       * @author potanin@UD
       * @since 0.1.1
       */
      public static function set( $key, $value = null ) {
        return self::$instance->_settings ? self::$instance->_settings->set( $key, $value ) : null;
      }

      /**
       * Get the Veneer Singleton
       *
       * Concept based on the CodeIgniter get_instance() concept.
       *
       * @example
       *
       *      var settings = Veneer::get_instance()->Settings;
       *      var api = Veneer::$instance()->API;
       *
       * @static
       * @return object
       *
       * @method get_instance
       * @for Veneer
       */
      public static function &get_instance() {
        return self::$instance;
      }

    }

  }

}

<?php
/**
 * UsabilityDynamics\Veneer Bootstrap
 *
 * @verison 0.2.0
 * @author potanin@UD
 * @namespace UsabilityDynamics\Veneer
 */
namespace UsabilityDynamics\Veneer {

  // Seek ./vendor/autoload.php and autoload
  if( is_file( basename( __DIR__ ) . DIRECTORY_SEPARATOR . 'vendor/autoload.php' ) ) {
    include_once( basename( __DIR__ ) . DIRECTORY_SEPARATOR . 'vendor/autoload.php' );
  }

  use UsabilityDynamics\Utility;
  use UsabilityDynamics\Settings;

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
        global $wpdb, $current_site, $current_blog;

        // Return singleton instance
        if( self::$instance ) {
          return self::$instance;
        }

        // Save context reference.
        self::$instance = & $this;

        // Identify site being requested
        if( !$current_site || !$current_blog ) {
          $this->identify_site();
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

        if( !$this->is_valid ) {
          wp_die( 'Invalid domain.' );
        }

        // Fix MultiSite URLs
        $this->fix_urls();

        // Initialize Controllers and Helpers
        $this->developer = new Developer();
        $this->settings  = new Settings();
        $this->media     = new Media();
        $this->mapping   = new Mapping();
        $this->api       = new API();

        // Must set or long will not work
        if( !defined( 'COOKIE_DOMAIN' ) ) {
          define( 'COOKIE_DOMAIN', $this->requested_domain );
        }

        // Initialize all else.
        add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
        add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 21 );

        if( defined( 'WP_PLUGIN_DIR' ) && defined( 'WP_PLUGIN_URL' ) ) {
          add_filter( 'plugins_url', array( $this, 'plugins_url' ), 10, 3 );
        }

      }

      /**
       * Identify Request
       *
       * @todo Add logic to fix symbolic links.
       *
       * @method identify_site
       */
      public function plugins_url( $url, $path, $plugin ) {

        if( strpos( $url, '/Users/potanin/Products' ) ) {
          $url = str_replace( '/Users/potanin/Products/', '/vendor/usabilitydynamics/', str_replace( '/modules', '', $url ) );
        }

        //echo "\n " . $plugin . ' - ' . $url;

        return $url;
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

        $_lookup = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}blogs WHERE domain = '{$_SERVER[HTTP_HOST]}' LIMIT 1" );

        $blog_id = $wpdb->blogid = $_lookup->blog_id;
        $site_id = $wpdb->siteid = $_lookup->site_id;

        $current_site          = $wpdb->get_row( "SELECT * from {$wpdb->prefix}site WHERE id = '{$site_id}' LIMIT 0,1" );
        $current_site->blog_id = $blog_id;

      }

      /**
       * Initializer.
       *
       * @method plugins_loaded
       * @author potanin@UD
       */
      public function plugins_loaded() {
        add_action( 'admin_init', array( $this, 'admin_init' ) );

        // add_action( 'shutdown', array( $this, 'shutdown' ) );
        // add_action( 'init', array( $this, 'init' ) );
        // add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
        // add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
        // add_action( 'wp_loaded', array( $this, 'wp_loaded' ) );
        // add_action( 'template_redirect', array( $this, 'template_redirect' ) );

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
          'id'     => 'network-themes',
          'title'  => __( 'Themes', self::$text_domain ),
          'href'   => network_admin_url( 'themes.php' ),
        ) );

        $wp_admin_bar->add_menu( array(
          'parent' => 'network-admin',
          'id'     => 'network-plugins',
          'title'  => __( 'Plugins', self::$text_domain ),
          'href'   => network_admin_url( 'plugins.php' ),
        ) );

        $wp_admin_bar->add_menu( array(
          'parent' => 'network-admin',
          'id'     => 'network-settings',
          'title'  => __( 'Settings', self::$text_domain ),
          'href'   => network_admin_url( 'settings.php' ),
        ) );

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

        if( !defined( 'BLOGUPLOADDIR' ) ) {

          if( defined( 'WP_VENEER_DOMAIN_MEDIA' ) && WP_VENEER_DOMAIN_MEDIA ) {
            define( 'BLOGUPLOADDIR', WP_BASE_DIR . '/media/' . Bootstrap::get_instance()->domain );
          } else {
            define( 'BLOGUPLOADDIR', WP_BASE_DIR . '/media/' . Bootstrap::get_instance()->site_id );
          }

        }

        add_filter( 'network_site_url', array( get_class(), 'network_site_url' ) );

        // add_filter( 'blog_option_upload_path', array( get_class(), 'blog_option_upload_path' ) );

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

        if( defined( 'WP_SYSTEM_DIRECTORY' ) && WP_SYSTEM_DIRECTORY != '' ) {
          return str_replace( 'wp-admin', 'system/wp-admin', $url );
        }

        return $url;

      }

      /**
       * Upload Path
       *
       *
       * @author potanin@UD
       * @method network_site_url
       *
       * @param $url
       *
       * @return mixed
       */
      public static function blog_option_upload_path( $url ) {

        // In WP 3.5 the UPLOADS constant sets the uploads path relative to the ABSPATH
        if( defined( 'UPLOADS' ) ) {

        }

        // Legacy WordPress MS
        if( strpos( $url, 'wp-content/blogs.dir' ) !== false ) {
          return str_replace( 'wp-content/blogs.dir', 'media/sites', $url );
        }

        // Contemporary WordPress MS
        if( strpos( $url, 'wp-content/sites' ) !== false ) {
          return str_replace( 'wp-content/sites', 'media/sites', $url );
        }

        if( strpos( $url, 'wp-content/uploads' ) !== false ) {
          return str_replace( 'wp-content/uploads', 'media/sites', $url );
        }

        return $url;

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

        die( 'Veneer error' );

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

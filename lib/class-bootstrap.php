<?php
/**
 * UsabilityDynamics\Cluster Bootstrap
 *
 * @verison 0.4.1
 * @author potanin@UD
 * @namespace UsabilityDynamics\Cluster
 */
namespace UsabilityDynamics\Cluster {

  if( !class_exists( 'UsabilityDynamics\Cluster\Bootstrap' ) ) {

    /**
     * Bootstrap Cluster
     *
     * @class Bootstrap
     * @author potanin@UD
     * @version 0.0.1
     */
    class Bootstrap {

      /**
       * Cluster core version.
       *
       * @static
       * @property $version
       * @type {Object}
       */
      public static $version = '0.4.1';

      /**
       * Textdomain String
       *
       * @public
       * @property text_domain
       * @var string
       */
      public static $text_domain = 'wp-cluster';

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
       * Original host, when proxied.
       * @public
       * @property $original_host
       * @type {String}
       * @var null
       */
      public $original_host = null;

      /**
       * Settings.
       *
       * @public
       * @property $_settings
       * @type {Mixed}
       */
      public $_settings = null;
      public $_mapping = null;
      public $_developer = null;
      public $_api = null;
      public $_theme = null;

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
        global $wpdb, $current_site, $current_blog, $wp_cluster;

        // Return Singleton Instance.
        if( self::$instance ) {
          return self::$instance;
        }

        if( !defined( 'MULTISITE' ) ) {
          wp_die( '<h1>Cluster Fatal Error.</h1><p>MULTISITE constant is not defined.</p>' );
        }

        if( !defined( 'UPLOADBLOGSDIR' ) ) {
          wp_die( '<h1>Cluster Fatal Error.</h1><p>UPLOADBLOGSDIR constant is not defined.</p>' );
        }

        // Save Instance.
        $wp_cluster = self::$instance = &$this;

        // Seek ./vendor/autoload.php and autoload
        if( is_file( basename( __DIR__ ) . DIRECTORY_SEPARATOR . 'vendor/autoload.php' ) ) {
          include_once( basename( __DIR__ ) . DIRECTORY_SEPARATOR . 'vendor/autoload.php' );
        }

        // Identify site being requested. This should be handled by sunrise.php.
        if( !$current_site || !$current_blog ) {
          $this->identify_site();
        }

        if( !$current_site ) {
          wp_die( '<h1>Cluster Fatal Error.</h1><p>Site not identified.</p>' );
        }

        // Current Site.
        $this->site_name        = get_option( 'blogname' );
        $this->cluster_domain   = WP_BASE_DOMAIN;
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
        $this->original_host    = $_SERVER[ 'HTTP_HOST' ];

        if( !$this->is_valid ) {
          wp_die( '<h1>Cluster Network Error</h1><p>Your request is for an invalid domain.</p>', $this->domain );
        }

        // Initialize Settings.
        $this->_settings();

        // Initialize Components.
        $this->_components();

        // Initialize Interfaces.
        $this->_interfaces();

        // Fix MultiSite URLs
        $this->_fix_urls();

        // Must set or long will not work
        if( !defined( 'COOKIE_DOMAIN' ) ) {
          define( 'COOKIE_DOMAIN', $this->requested_domain );
        }

        // Initialize all else.
        add_action( 'plugins_loaded', array( &$this, 'plugins_loaded' ) );
        add_action( 'admin_bar_menu', array( &$this, 'admin_bar_menu' ), 21 );

        add_action( 'init', array( &$this, 'init' ) );
        add_action( 'admin_init', array( &$this, 'admin_init' ) );
        add_action( 'admin_menu', array( &$this, 'admin_menu' ), 500 );
        add_action( 'template_redirect', array( &$this, 'template_redirect' ) );

        // Add Cluster Scripts & Styles.
        add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ), 20 );

        // Modify Core UI.
        add_filter( 'admin_footer_text', array( &$this, 'admin_footer_text' ) );
        add_action( 'manage_sites_custom_column', array( &$this, 'manage_sites_custom_column' ), 10, 2 );

        if( is_network_admin() ) {
          add_action( 'network_admin_menu', array( &$this, 'network_admin_menu' ), 100 );
        }

      }

      /**
       * Initialize Settings.
       *
       */
      private function _settings() {

        // Initialize Settings.
        $this->_settings = new Settings(array(
          "store" => "options",
          "key"   => "ud:cluster",
        ));

        // ElasticSearch Service Settings.
        $this->set( 'documents', array(
          "active" => true,
          "host"   => "localhost",
          "port"   => 9200,
          "token"  => null,
        ));

        // Save Settings.
        // $this->_settings->commit();

      }

      /**
       * Initialize Media, Varnish, etc.
       *
       */
      private function _components() {

        // Initialize Controllers and Helpers
        $this->_developer = new Developer();
        $this->_settings  = new Settings();
        $this->_mapping   = new Mapping();
        $this->_api       = new API();
        $this->_theme     = new Theme();

        // Enable CDN Media.
        //$this->_media = new Media( $this->get( 'media' ) );

        // Enable Varnish.
        //$this->_varnish = new Varnish($this->get( 'varnish' ));

      }

      /**
       * Modify Admin Footer Text
       *
       */
      public function admin_footer_text() {
        return '<span id="footer-thankyou">' . __( 'Provided by the <a href="http://' . $this->network_domain . '">' . $this->organization . '</a>' ) . ' network.</span>';
      }


      /**
       * Output Fatal Error Message
       *
       * @param $message
       */
      public function fatal( $message ) {
        wp_die( '<h1>Cluster Error</h1><p>' . $message . '</p>' );
      }

      /**
       * Initialize Interface Compnents
       *
       */
      private function _interfaces() {

        // Render Toolbar.
        add_action( 'wp_before_admin_bar_render', array( &$this, 'toolbar' ), 10 );

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
      private function _fix_urls() {

        // Add handling for /manage
        //add_filter( 'network_site_url', array( get_class(), 'network_site_url' ) );
        //add_filter( 'network_admin_url', array( get_class(), 'network_site_url' ) );

        // Add handling for /manage
        //add_filter( 'admin_url', array( get_class(), 'admin_url' ) );

      }

      /**
       * Add Cluster Toolbar
       *
       * @method cluster_toolbar
       * @for Boostrap
       */
      public function toolbar() {
        global $wp_admin_bar;

        $wp_admin_bar->add_menu(array(
          'id'    => 'cluster',
          'meta'  => array(
            'html'     => '<div class="cluster-toolbar-info"></div>',
            'target'   => '',
            'onclick'  => '',
            'tabindex' => 10,
            'class'    => 'cluster-toolbar'
          ),
          'title' => 'Cluster',
          'href'   => $this->cluster_domain . '/manage/admin.php?page=cluster#panel=networks'
        ));

        $wp_admin_bar->add_menu(array(
          'parent' => 'cluster',
          'id'     => 'cluster-network',
          'meta'   => array(),
          'title'  => 'Networks',
          'href'   => $this->cluster_domain . '/manage/admin.php?page=cluster#panel=networks'
        ));

        $wp_admin_bar->add_menu(array(
          'parent' => 'cluster',
          'id'     => 'cluster-dns',
          'meta'   => array(),
          'title'  => 'DNS',
          'href'   => $this->cluster_domain . '/manage/admin.php?page=cluster#panel=dns'
        ));

      }

      /**
       * Added by anton.
       */
      public function init() {
        // global $wpi_xml_server;
        // $wpi_xml_server = new \UsabilityDynamics\UD_XMLRPC( 'https://raas.usabilitydynamics.com:443', \UsabilityDynamics\get_option('ud_api_public_key'), 'WordPress/3.7.1', 'ud' );
      }

      /**
       *
       */
      public function admin_enqueue_scripts() {
        wp_enqueue_style( 'cluster-app', home_url( '/vendor/usabilitydynamics/wp-cluster/styles/app.css' ), array(), self::$version );
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
            echo '<img src="" class="cluster-site-thumbnail"/>';
          break;

        }

      }

      /**
       * Site Administration Menus.
       *
       * @method admin_menu
       */
      public function admin_menu() {

        // Add Site Administration (Settings -> Cluster).
        add_submenu_page( 'options-general.php', __( 'Cluster', self::$text_domain ), __( 'Cluster', self::$text_domain ), 'manage_network', 'cluster', function() {
          include( dirname( __DIR__ ) . '/views/site-settings.php' );
        });

      }

      /**
       * Network Administration Menu.
       *
       * network_admin_url( 'cluster-icon.png' )
       */
      public function network_admin_menu() {

        // Only admin can see W3TC notices and errors
        // add_action('admin_notices', array( &$this, 'admin_notices' ));
        // add_action('network_admin_notices', array( &$this, 'admin_notices' ));

        // Add Network Administration.
        add_menu_page( __( 'Cluster', self::$text_domain ), __( 'Cluster', self::$text_domain ), 'manage_network', 'cluster-dashboard', function() {
          include( dirname( __DIR__ ) . '/views/network-settings.php' );
        });

        add_submenu_page( 'cluster-dashboard', __( 'DNS', self::$text_domain ), __( 'DNS', self::$text_domain ), 'manage_network', 'cluster-dns', function() {
          include( dirname( __DIR__ ) . '/views/-settings.php' );
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

        // remove_role( 'manage.stuff' );
        // $result = add_role( 'manage.stuff', __( 'Manage Stuff' ), array( "manage.stuff.view" => true, "manage.stuff.edit" => true ) );
        // $role = get_role( 'manage.stuff' );
        // $current_user = wp_get_current_user();
        // $current_user->add_role( 'administrator' );
        // $current_user->add_role( 'manage.stuff' );
        // die( '<pre>' . print_r( $current_user , true ) . '</pre>' );

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
          header( 'server: Cluster' );
          header( 'x-Powered-by: Cluster ' . Bootstrap::$version );
          header( 'x-Cluster-version:' . Bootstrap::$version );
        }

      }

      /**
       * Initialize Admin
       *
       * @method admin_init
       * @author potanin@UD
       */
      public function admin_init() {
        remove_action( 'admin_notices', 'akismet_warning' );
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

        wp_die( 'Cluster error' );

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
       *    Cluster::get( 'my_key' )
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
       *    Cluster::set( 'my_key', 'my-value' )
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
       * Get the Cluster Singleton
       *
       * Concept based on the CodeIgniter get_instance() concept.
       *
       * @example
       *
       *      var settings = Cluster::get_instance()->Settings;
       *      var api = Cluster::$instance()->API;
       *
       * @static
       * @return object
       *
       * @method get_instance
       * @for Cluster
       */
      public static function &get_instance() {
        return self::$instance;
      }

    }

  }

}

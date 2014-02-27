<?php
/**
 * UsabilityDynamics\Cluster Bootstrap
 *
 * ### Options
 * * hide.toolbar.login
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
          return $this;
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

        // Fatal Error Handler.
        register_shutdown_function( array( $this, '_shutdown' ) );

        // Initialize Settings.
        $this->_settings();

        // Initialize Components.
        $this->_components();

        // Initialize Interfaces.
        $this->_interfaces();

        // Fix MultiSite URLs
        $this->_fix_urls();

        // Prepare Cookie Constants.
        $this->_cookie();

        // Sent common response headers.
        $this->_send_headers();

        // Initialize all else.
        add_action( 'plugins_loaded', array( &$this, 'plugins_loaded' ) );
        add_action( 'admin_bar_menu', array( &$this, 'admin_bar_menu' ), 21 );
        add_action( 'add_admin_bar_menus', array( &$this, 'add_admin_bar_menus' ), 21 );

        add_action( 'init', array( &$this, 'init' ) );
        add_action( 'admin_init', array( &$this, 'admin_init' ) );
        add_action( 'admin_menu', array( &$this, 'admin_menu' ), 500 );

        // Send Cluster Headers.
        add_action( 'init', array( &$this, '_send_headers' ) );

        // Add Cluster Scripts & Styles.
        add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ), 20 );
        
        // Modify Core UI.
        add_filter( 'admin_footer_text', array( &$this, 'admin_footer_text' ) );
        add_action( 'manage_sites_custom_column', array( &$this, 'manage_sites_custom_column' ), 10, 2 );

        add_filter( 'pre_update_option_rewrite_rules', array( $this, '_update_option_rewrite_rules' ), 1 );

        // /manage/admin-ajax.php?action=cluster_uptime_status
        add_action( 'wp_ajax_cluster_uptime_status', array( $this, '_uptime_status' )  );
        add_action( 'wp_ajax_nopriv_cluster_uptime_status', array( $this, '_uptime_status' ) );

        add_action( 'wp_ajax_nopriv_varnish_test', array( $this, '_varnish_test' )  );
        add_action( 'wp_ajax_varnish_test', array( $this, '_varnish_test' )  );

        add_filter( 'wp_mail_from', array( 'Utility', 'wp_mail_from' ), 10 );
        add_filter( 'wp_mail_from_name', array( 'Utility', 'wp_mail_from_name' ), 10 );

        if( is_network_admin() ) {
          add_action( 'network_admin_menu', array( &$this, 'network_admin_menu' ), 100 );
        }
        
      }

      public function _shutdown() {


        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {}

        if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {}

        $errfile = 'unknown file';
        $errstr  = 'shutdown';
        $errno   = E_CORE_ERROR;
        $errline = 0;

        $error = error_get_last();

        if( $error !== NULL) {
          $errno   = $error[ 'type' ];
          $errfile = $error[ 'file' ];
          $errline = $error[ 'line' ];
          $errstr  = $error[ 'message' ];
          // die( '<pre>' . print_r( $error, true ) . '</pre>' );
          // self::fatal();
        }

      }

      /**
       *
       */
      private function _cookie() {
        
        $siteurl = get_site_option( 'siteurl' );
        
        // Must set or long will not work
        if( !defined( 'COOKIE_DOMAIN' ) ) {
          define( 'COOKIE_DOMAIN', $this->requested_domain );
        }
                
        if ( !defined( 'COOKIEHASH' ) ) {
          
          if ( $siteurl ) {
          	define( 'COOKIEHASH', md5( $siteurl ) );
          } else {            
           	define( 'COOKIEHASH', '' );
          }
          
        }
        
        if( !defined( 'USER_COOKIE' ) ) {
          define( 'USER_COOKIE', 'wordpressuser_' . COOKIEHASH );
        }

        if( !defined( 'LOGGED_IN_COOKIE' ) ) {
          define( 'LOGGED_IN_COOKIE', 'wordpress_logged_in_' . COOKIEHASH );
        }

        if( !defined( 'COOKIEPATH' ) ) {
          define( 'COOKIEPATH', preg_replace( '|https?://[^/]+|i', '', get_option( 'home' ) . '/' ) );
        }

        if( !defined( 'SITECOOKIEPATH' ) ) {
          define( 'SITECOOKIEPATH', preg_replace( '|https?://[^/]+|i', '', get_option( 'siteurl' ) . '/' ) );
        }

        if( !defined( 'ADMIN_COOKIE_PATH' ) ) {
          define( 'ADMIN_COOKIE_PATH', SITECOOKIEPATH . 'wp-admin' );
        }

        if( !defined( 'PLUGINS_COOKIE_PATH' ) ) {
          define( 'PLUGINS_COOKIE_PATH', preg_replace('|https?://[^/]+|i', '', WP_PLUGIN_URL)  );
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
        $this->_log       = new Log();
        //$this->_media    = new Media( $this->get( 'media' ) );
        //$this->_varnish = new Varnish($this->get( 'varnish' ));

        // Basic Frontend Security
        remove_action( 'wp_head', 'feed_links', 2 );
        remove_action( 'wp_head', 'feed_links_extra', 3 );
        remove_action( 'wp_head', 'rsd_link' );
        remove_action( 'wp_head', 'wlwmanifest_link' );
        remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
        remove_action( 'wp_head', 'wp_generator' );
        remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

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
      public function fatal( $message = null ) {
        wp_die( $message ? '<h1>Cluster Error</h1><p>' . $message . '</p>' : '<hr />' );
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

        $wp_admin_bar->remove_menu( 'wp-logo' );
        $wp_admin_bar->remove_menu( 'comments' );

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
        global $submenu;

        // die( '<pre>' . print_r( $submenu, true ) . '</pre>' );

        remove_submenu_page( 'index.php', 'my-sites.php' );
        remove_submenu_page( 'index.php', 'my-networks' );

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
       * Modify Rewrite Rules on Save.
       *
       * @todo Fix rule - does not seem to work.
       * @param $rules
       * @internal param $value
       * @return array
       */
      public function _update_option_rewrite_rules( $rules ) {

        // Define New Rules.
        $new_rules = array(
          '^api/uptime-status/?' => str_replace( trailingslashit( site_url() ), '', admin_url( 'admin-ajax.php?action=cluster_uptime_status' ) ),
        );

        // Return concatenated rules.
        return $new_rules + $rules;

      }

      /**
       *
       */
      public function _uptime_status() {

        ob_start( array( $this, '_render_status' ) );

      }

      /**
       * Renders on "shutdown" filter.
       *
       */
      public function _render_status( $buffer, $errstr,  $errfile, $errline, $errcontext ) {
        global $wp, $wpdb;

        $have_error = false;

        //trigger_error("Cannot divide by zero", E_USER_ERROR);

        if ( $error = error_get_last() ) {
          switch( $error['type'] ){
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
              $have_error = true;
            break;
          }
        }

        header( "Content-type: application/json; charset: UTF-8" );
        header( "Cache-Control: private, must-revalidate" );

        if( !$have_error ) {
          header( ':', true, 200 );
        } else {
          header( ':', true, 200 );
        }

        $buffer = json_encode(array(
          "ok" => true,
          "message" => $have_error ? sprintf( __( "Error occured. Message: %s", self::$text_domain ), $error[ 'message' ] ) : __( "Service is fully operational.", self::$text_domain ),
          "took" => timer_stop(),
          "stats" => array(
            "queries" => $wpdb->num_queries
          )
        ));

        return $buffer;

      }

      /**
       * Add Frontend Headers
       *
       */
      public function _send_headers() {

        if( !headers_sent() ) {
          header( 'X-Server: WP-Veneer ' . Bootstrap::$version );
          header( 'X-Powered-By: WP-Cluster ' . Bootstrap::$version );
          header( 'X-Cluster-Version:' . Bootstrap::$version );
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
       * Modify Admin Bar Menus Hooks.
       *
       * @param bool $wp_admin_bar
       */
      public function add_admin_bar_menus( $wp_admin_bar = false ) {

        if( $this->get( 'hide.toolbar.login' ) ) {
          remove_action( 'admin_bar_menu', 'wp_admin_bar_my_account_menu', 0 );
          remove_action( 'admin_bar_menu', 'wp_admin_bar_my_account_item', 7 );
        }


      }

      /**
       * Change My Account Toolbar Dropdown.
       *
       * @method admin_bar_menu
       * @author potanin@UD
       */
      public function my_account_toolbar( $wp_admin_bar = false ) {

        $user_id      = get_current_user_id();
        $current_user = wp_get_current_user();
        $profile_url  = get_edit_profile_url( $user_id );

        $wp_admin_bar->remove_node( 'my-account' );

        $wp_admin_bar->add_menu( array(
          'id'        => 'my-account',
          'parent'    => 'top-secondary',
          'title'     => sprintf( __('%1$s'), $current_user->display_name ),
          'href'      => $profile_url,
          'meta'      => array(
            'class'     => $class,
            'title'     => __('My Account'),
          ),
        ));

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
       * Testing Varnish
       *
       * @temp
       */
      public static function _varnish_test() {

        // AJAX Request.
        if( Utility::requestHeaders()->{'X-Requested-With'} === "XMLHttpRequest" ) {

        }

        // Veneer/Varnish API Proxy.
        if( Utility::requestHeaders()->{'X-Veneer-Proxy'} === "true" ) {

        }

        // CloudFront / Varnish Essetnails
        header( "Pragma: public" );
        header( "Cache-Control: public, must-revalidate, max-age=2592000" ); //  CF will not cache at all if set: no-cache='Set-Cookie'
        header( 'Expires: ' . gmdate('D, d M Y H:i:s', time() + 2592000 ) . ' GMT' );


        header( "Vary: Accept-Encoding" );
        header( "Content-Type: application/json" );

        // header( "X-Api-Response: true" );
        // header( "Content-Length: ..." );

        // header_remove( 'X-Content-Type-Options' );
        // header_remove( 'X-Powered-By' );
        // header_remove( 'X-Frame-Options' );
        // header_remove( 'X-Robots-Tag' );

        die(json_encode(array(
          "varnish-request-id" => Utility::requestHeaders()->{'X-Varnish'},
          "request-headers" => Utility::requestHeaders(),
          "ok" => true,
          "id" => time(),
          "message"=> __( 'Hello!' ),
          "data"=> array(
            "key" => "value"
          )
        )));

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

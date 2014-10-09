<?php
namespace UsabilityDynamics\wpElastic {

  use UsabilityDynamics;

  if( !class_exists( 'UsabilityDynamics\wpElastic\Bootstrap' ) ) {

    /**
     * @property string locale
     * @property string version
     */
    class Bootstrap {

      /**
       * Singleton Instance Reference.
       *
       * @public
       * @static
       * @property $instance
       * @type {Object}
       */
      public static $instance = null;

      /**
       * -
       *
       * @property $basename
       * @type {Object}
       */
      public $basename = 'wp-elastic';

      /**
       * RPC Slug
       *
       * @property $slug
       * @type {String}
       */
      public $slug = 'wpe';

      /**
       * -
       *
       * @property $basename
       * @type {Object}
       */
      public $file = null;

      /**
       * -
       *
       * @property $basename
       * @type {Object}
       */
      public $path = null;

      /**
       * -
       *
       * @property $basename
       * @type {Object}
       */
      public $url = null;

      /**
       * -
       *
       * @property $basename
       * @type {Object}
       */
      public $relative = null;

      /**
       * Settings Instance.
       *
       * @property $_settings
       * @type {Object}
       */
      private $_settings;

      /**
       * Pages.
       *
       * @property $_pages
       * @type {Object}
       */
      private $_pages = array();

      /**
       *
       */
      function __construct() {
        global $wp_elastic;

        // Set singleton instance.
        self::$instance = &$this;

        try {

          // Check if being called too early, such as during Unit Testing.
          if( !function_exists( 'did_action' ) ) {
            return $this;
          }

          if( !class_exists( 'UsabilityDynamics\wpElastic\Utility' ) ) {
            require_once( 'class-elastic-utility.php' );
          }

          if( !class_exists( 'UsabilityDynamics\wpElastic\Settings' ) ) {
            require_once( 'class-elastic-settings.php' );
          }

          if( !class_exists( 'UsabilityDynamics\wpElastic\Events' ) ) {
            require_once( 'class-elastic-events.php' );
          }

          // Set Essentials.
          $this->file         = wp_normalize_path( dirname( __DIR__ ) . '/wp-elastic.php' );
          $this->basename     = plugin_basename( dirname( __DIR__ ) . '/wp-elastic.php' );
          $this->path         = plugin_dir_path( dirname( __DIR__ ) . '/wp-elastic.php' );
          $this->url          = plugins_url( '', dirname( __DIR__ ) );
          $this->relative     = str_replace( trailingslashit( WP_PLUGIN_DIR ), '', $this->file );

          // Initialize Settings and set defaults.
          $this->_settings = new Settings( array(
            'store' => 'site_meta',
            'key'   => 'wp-elastic'
          ));

          // Initialize Settings and set defaults.
          $this->_transient = new Settings( array(
            'store' => 'site_transient',
            'expiration' => 600,
            'key'   => 'wp-elastic',
          ));

          // Set Computed Options.
          $this->set( get_file_data( ( dirname( __DIR__ ) . '/wp-elastic.php' ), array(
            'name' => 'Plugin Name',
            'uri' => 'Plugin URI',
            'description' => 'Description',
            'version' => 'Version',
            'locale' => 'Text Domain'
          )));

          // Define runtime directory paths.
          $this->set( '__dir', array(
            'cache'     => defined( 'WP_ELASTIC_CACHE_DIR' )      ? WP_ELASTIC_CACHE_DIR      : dirname( __DIR__ ) . '/static/cache',
            'schemas'   => defined( 'WP_ELASTIC_SCHEMAS_DIR' )    ? WP_ELASTIC_SCHEMAS_DIR    : dirname( __DIR__ ) . '/static/schemas',
            'scripts'   => defined( 'WP_ELASTIC_SCRIPTS_DIR' )    ? WP_ELASTIC_SCRIPTS_DIR    : dirname( __DIR__ ) . '/static/scripts',
            'styles'    => defined( 'WP_ELASTIC_STYLES_DIR' )     ? WP_ELASTIC_STYLES_DIR     : dirname( __DIR__ ) . '/static/styles',
            'views'     => defined( 'WP_ELASTIC_VIEWS_DIR' )      ? WP_ELASTIC_VIEWS_DIR      : dirname( __DIR__ ) . '/static/views'
          ));

          // Pluggable file Locations.
          $this->set( '__file', array(
            'rest'      => defined( 'WP_ELASTIC_REST_FILE' )      ? WP_ELASTIC_REST_FILE      : dirname( __DIR__ ) . '/lib/api/rest-actions.php',
            'template'  => defined( 'WP_ELASTIC_TEMPLATE_FILE' )  ? WP_ELASTIC_TEMPLATE_FILE  : dirname( __DIR__ ) . '/lib/api/template.php',
          ));

          // @note Temporary until options UI is ready.
          $this->set( 'options', array(
            'load_default_schemas'  => true,
            'public_types'          => array( 'post', 'page' ),
            'private_types'         => array( 'media' ),
            'sync_users'            => true,  // array( 'subscriber', 'editor' )
            'sync_terms'            => true   // array( 'category' )
          ));

          // Verify Dependency Versions.
          $this->checkDependencies();

        } catch( Exception $e ) {
          _doing_it_wrong( 'wpElastic\Bootstrap::__construct', $e->getMessage(), method_exists( $this, 'get' ) ? $this->get( 'version' ) : 'undefined' );
          return new \WP_Error( $e->getMessage() );
        }

        // Upgrade Control.
        register_uninstall_hook(    dirname( __DIR__ ) . '/wp-elastic.php',   array( 'wpElastic', 'uninstall' ) );
        register_activation_hook(   dirname( __DIR__ ) . '/wp-elastic.php',   array( 'wpElastic', 'activate' ) );
        register_deactivation_hook( dirname( __DIR__ ) . '/wp-elastic.php',   array( 'wpElastic', 'deactivate' ) );

        // Core Actions.
        add_action( 'init',                           array( $this, 'init' ), 20 );
        add_action( 'admin_init',                     array( $this, 'admin_init' ), 20 );
        add_action( 'admin_menu',                     array( $this, 'admin_menu' ), 20 );
        add_action( 'network_admin_menu',             array( $this, 'admin_menu' ), 20 );
        add_action( 'wp_before_admin_bar_render',     array( $this, 'toolbar' ), 10 );
        add_action( 'admin_enqueue_scripts',          array( $this, 'enqueue_scripts' ), 200 );
        add_action( 'wp_enqueue_scripts',             array( $this, 'enqueue_scripts' ), 20 );
        add_action( 'shutdown',                       array( $this, 'shutdown' ), 100 );

        // Synchroniation Events.
        add_action( 'deleted_user',                   array( 'UsabilityDynamics\wpElastic\Events', 'deleted_user' ) );
        add_action( 'profile_update',                 array( 'UsabilityDynamics\wpElastic\Events', 'user_update' ) );
        add_action( 'user_register',                  array( 'UsabilityDynamics\wpElastic\Events', 'user_update' ) );
        add_action( 'added_user_meta',                array( 'UsabilityDynamics\wpElastic\Events', 'user_meta_change' ) );
        add_action( 'updated_user_meta',              array( 'UsabilityDynamics\wpElastic\Events', 'user_meta_change' ) );
        add_action( 'deleted_user_meta',              array( 'UsabilityDynamics\wpElastic\Events', 'user_meta_change' ) );
        add_action( 'save_post',                      array( 'UsabilityDynamics\wpElastic\Events', 'save_post' ), 10, 2 );
        add_action( 'delete_post',                    array( 'UsabilityDynamics\wpElastic\Events', 'delete_post' ) );
        add_action( 'trash_post',                     array( 'UsabilityDynamics\wpElastic\Events', 'delete_post' ) );
        add_action( 'trash_post',                     array( 'UsabilityDynamics\wpElastic\Events', 'delete_post' ) );
        add_action( 'edit_term',                      array( 'UsabilityDynamics\wpElastic\Events', 'edit_term' ), 10, 3 );

        // Utility Actions.
        add_filter( 'plugin_action_links_' . $this->basename, array( 'UsabilityDynamics\wpElastic\Bootstrap', 'action_links' ), -10 );

      }

      /**
       * Shutdown Handler
       *
       * @author potanin@UD
       * @method shutdown
       */
      public function shutdown() {

        if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {}

        if( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {}

        Service::processQueue();

      }

      /**
       * Intialize Models
       *
       * @author potanin@UD
       * @method init
       */
      public function init() {

        if( $this->get( 'options.load_default_schemas' ) && $this->get( '__dir.schemas' ) ) {
          Utility::load_schemas( $this->get( '__dir.schemas' ) );
        }

        // Constant Overrides.
        $this->set( 'service.url',          defined( 'WP_ELASTIC_SERVICE_URL' )   ? WP_ELASTIC_SERVICE_URL    : $this->get( 'service.url' ) );
        $this->set( 'service.index',        defined( 'WP_ELASTIC_SERVICE_INDEX' ) ? WP_ELASTIC_SERVICE_INDEX  : $this->get( 'service.index' ) );
        $this->set( 'service.secret_key',   defined( 'WP_ELASTIC_SECRET_KEY' )    ? WP_ELASTIC_SECRET_KEY     : $this->get( 'service.secret_key' ) );
        $this->set( 'service.public_key',   defined( 'WP_ELASTIC_PUBLIC_KEY' )    ? WP_ELASTIC_PUBLIC_KEY     : $this->get( 'service.public_key' ) );
        $this->set( 'api.access_token',     defined( 'WP_ELASTIC_ACCESS_TOKEN' )  ? WP_ELASTIC_ACCESS_TOKEN   : $this->get( 'api.access_token' ) );
        $this->set( 'defaults.locale',      defined( 'WPLANG' )                   ? WPLANG                    : $this->get( 'defaults.locale' ) );

        // Get Rest Handler File.
        if( $this->get( '__file.rest' ) && is_file( $this->get( '__file.rest' ) ) ) {
          require_once( $this->get( '__file.rest' ) );

          // Search API
          API::define( '/v1/search', array(
            'handler'     => 'wpElasticSearchAPI',
            'namespace'   => 'wp-elastic',
            'parameters'  => array( 'name', 'version' ),
            'scopes'      => array( 'manage_options' )
          ));

          // Document API.
          API::define( '/v1/document', array(
            'handler'     => 'wpElasticDocumentAPI',
            'namespace'   => 'wp-elastic',
            'parameters'  => array( 'name', 'version' ),
            'scopes'      => array( 'manage_options' )
          ));

          // Service API.
          API::define( '/v1/service', array(
            'handler'     => 'wpElasticServiceAPI',
            'namespace'   => 'wp-elastic',
            'parameters'  => array( 'name', 'version' ),
            'scopes'      => array( 'manage_options' )
          ));

        }

      }

      /**
       * Check Dependency Versions.
       *
       * @throws Exception
       */
      private function checkDependencies() {

        // Check UsabilityDynamics\Settings version.
        if( version_compare( '0.2.1', Settings::$version ) >= 1 ) {
          throw new Exception( __( sprintf( 'Settings library version is invalid, wpElastic requires 0.2.1 or higher, while %s is available.', Settings::$version ),  $this->get( 'locale' ) ) );
        };

      }

      /**
       * @param $links
       *
       * @return array
       */
      public function action_links( $links ) {
        $links[] = '<a href="options-general.php?page=elastic_search"><b>Settings</b></a>';
        $links[] = '<a target="_blank" href="https://github.com/UsabilityDynamics/wp-elastic/wiki"><b>Documentation</b></a>';
        return $links;
      }

      /**
       *
       */
      public function admin_init() {

      }

      /**
       * Shows Veneer Status (in dev)
       *
       * @method toolbar
       * @for Boostrap
       */
      public function toolbar() {
        global $wp_admin_bar;

        if( !$this->get( 'options.enable.toolbar' ) ) {
          // return;
        }

        $wp_admin_bar->add_menu( array(
          'id'    => 'wp-elastic',
          'parent'    => 'top-secondary',
          'meta'  => array(
            'html'     => '<div class="wp-elastic-toolbar-info"></div>',
            'target'   => '',
            'onclick'  => '',
            'title'    => __( 'wpElastic', $this->get( 'locale' ) ),
            'tabindex' => 10,
            'class'    => 'wp-elastic-toolbar'
          ),
          'title' => __( 'wpElastic', $this->get( 'locale' ) ),
          'href'  => network_admin_url( 'admin.php?page=wp-elastic' )
        ));

        $wp_admin_bar->add_menu( array(
          'parent' => 'wp-elastic',
          'id'     => 'wp-elastic-pagespeed',
          'meta'   => array(),
          'title'  => 'PageSpeed',
          'href'   => network_admin_url( 'admin.php?page=wp-elastic#panel=cdn' )
        ));

        $wp_admin_bar->add_menu( array(
          'parent' => 'wp-elastic',
          'id'     => 'wp-elastic-cloudfront',
          'meta'   => array(),
          'title'  => 'CloudFront',
          'href'   => network_admin_url( 'admin.php?page=wp-elastic#panel=cdn' )
        ));

        $wp_admin_bar->add_menu( array(
          'parent' => 'wp-elastic',
          'id'     => 'wp-elastic-varnish',
          'meta'   => array(),
          'title'  => 'Varnish',
          'href'   => network_admin_url( 'admin.php?page=wp-elastic#panel=cdn' )
        ));

      }

      /**
       *
       */
      public function admin_menu() {
        global $menu, $submenu;

        // Site Only.
        if( current_filter() === 'admin_menu' ) {
          $this->_pages[ 'services' ] = add_options_page(   __( 'wpElastic', $this->get( 'locale' ) ),  __( 'wpElastic', $this->get( 'locale' ) ), 'manage_options', 'wp-elastic-service', array( $this, 'admin_template' ) );
          $this->_pages[ 'tools' ]    = add_dashboard_page( __( 'wpElastic', $this->get( 'locale' ) ),  __( 'wpElastic', $this->get( 'locale' ) ),  'manage_options', 'wp-elastic-tools',   array( $this, 'admin_template' ) );
        }

        // Network Only.
        if( current_filter() === 'network_admin_menu' ) {
          $this->_pages[ 'services' ] = add_options_page( __( 'wpElastic', $this->get( 'locale' ) ), __( 'wpElastic', $this->get( 'locale' ) ), 'manage_options', 'wp-elastic-service', array( $this, 'admin_template' ) );
          $this->_pages[ 'reports' ]  = add_submenu_page( 'index.php', __( 'Reports', $this->get( 'locale' ) ), __( 'Reports', $this->get( 'locale' ) ), 'manage_options', 'wp-elastic-reports', array( $this, 'admin_template' ) );
        }

      }

      /**
       * Load Admin Template.
       *
       */
      public function admin_template() {

        $_path = $this->path . 'static/views/' . str_replace( array( 'dashboard_page_', 'plugins_page_', 'settings_page_', 'tools_page_'  ), '', get_current_screen()->id ) . '.php';

        if( file_exists( $_path ) ) {
          include( $_path );
        }

      }

      /**
       *
       * @action admin_enqueue_scripts
       */
      public function enqueue_scripts() {

        // Register Libraies.
        wp_register_script( 'udx-requires',         '//cdn.udx.io/udx.requires.js', array(), $this->get( 'version' ), true );
        wp_register_script( 'wp-elastic.admin',     plugins_url( '/static/scripts/wp-elastic.admin.js',     dirname( __DIR__ ) ),  array( 'udx-requires' ),  $this->get( 'version' ), true );
        wp_register_script( 'wp-elastic.mapping',   plugins_url( '/static/scripts/wp-elastic.mapping.js',   dirname( __DIR__ ) ),  array( 'udx-requires' ),  $this->get( 'version' ), true );
        wp_register_script( 'wp-elastic.settings',  plugins_url( '/static/scripts/wp-elastic.settings.js',  dirname( __DIR__ ) ),  array( 'udx-requires' ),  $this->get( 'version' ), true );

        // Register Styles.
        wp_register_style( 'wp-elastic.toolbar',    plugins_url( '/static/styles/wp-elastic.toolbar.css',   dirname( __DIR__ ) ),  array(), $this->get( 'version' ), 'all' );
        wp_register_style( 'wp-elastic',            plugins_url( '/static/styles/wp-elastic.css',           dirname( __DIR__ ) ),  array(), $this->get( 'version' ), 'all' );

        // Enable for Post Editers.
        if( current_filter() === 'admin_enqueue_scripts' && get_current_screen()->base === 'post' ) {
          wp_enqueue_script( 'udx-requires' );
          wp_enqueue_style( 'wp-elastic' );
          add_action( 'admin_print_footer_scripts', array( $this, 'admin_script_debug' ), 100 );
        }

        // Enable on Admin Pages.
        if( current_filter() === 'admin_enqueue_scripts' &&  in_array( get_current_screen()->id, $this->_pages ) ) {
          wp_enqueue_script( 'udx-requires' );
          wp_enqueue_style( 'wp-elastic' );
          add_action( 'admin_print_footer_scripts', array( $this, 'admin_script_debug' ), 100 );
        }

        // Global Toolbar.
        if( is_admin_bar_showing() ) {
          wp_enqueue_style( 'wp-elastic.toolbar' );
        }

        // Frontend Scripts.
        if( current_filter() === 'wp_enqueue_scripts' && is_admin_bar_showing() ) {}

      }

      /**
       * Local Development
       *
       */
      static function admin_script_debug() {

        if( defined( 'WP_ELASTIC_BASEURL' ) && WP_ELASTIC_BASEURL ) {
          echo '<script>"function" === typeof require ? require.config({ "baseUrl": "' . WP_ELASTIC_BASEURL . '"}) : console.error( "wp-elastic", "udx.require.js not found" );</script>';
        }

      }

      /**
       * Set Defaults on Activation.
       *
       * @author potanin@UD
       * @method activate
       */
      static public function activate() {

        // Initialize bootstrap.
        $instance = new Bootstrap;

        // $defaults = json_decode( file_get_contents( $this->path . 'static/schemas/wp-elastic.defaults.json' ));

        // Set Defaults.
        if( !$instance->get( '_installed' ) ) {
          $instance->set( array() );
        }

        $instance->set( '_installed',   true );
        $instance->set( '_status',      'active' );
        $instance->set( '_activated',   time() );

        if( !is_dir( dirname( __DIR__ ) . '/static/cache' ) ) {
          wp_mkdir_p( dirname( __DIR__ ) . '/static/cache' );
        }

        // Save Settings on activation.
        $instance->_settings->commit();

      }

      /**
       * Set Inactive Statuf Flag on Deactivation.
       *
       * @author potanin@UD
       * @method deactivate
       */
      static public function deactivate() {

        $instance = new wpElatic;

        $instance->set( '_status', 'inactive' );

        $instance->_settings->commit();

      }

      /**
       * Uninstall Plugin.
       *
       * Must be static.
       *
       */
      static public function uninstall() {

        // $this->set( '_status', 'uninstalled' );
        // $this->_settings->commit();

      }

      /**
       * Determine if instance already exists and Return Theme Instance
       *
       */
      public static function get_instance( $args = array() ) {
        return ( null === self::$instance ) ? new Bootstrap() : self::$instance;
      }

      /**
       * @param null $key
       * @param null $value
       *
       * @return \UsabilityDynamics\Settings
       */
      public function set( $key = null, $value = null ) {
        return $this->_settings->set( $key, $value );
      }

      /**
       * @param null $key
       * @param null $default
       *
       * @return \UsabilityDynamics\type
       */
      public function get( $key = null, $default = null ) {
        return $this->_settings->get( $key, $default );
      }

    }

  }

}
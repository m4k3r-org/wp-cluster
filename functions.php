<?php
  /**
   * Flawless
   *
   * Premium WordPress Theme - functions and definitions.
   *
   * @module Flawless
   * @namespace Flawless
   *
   * @author team@UD
   * @version 0.1.1
   */
  namespace Flawless {

    /**
     * Flawless
     *
     * Premium WordPress Theme - functions and definitions.
     *
     * @final
     *
     * @version 0.1.1
     * @author team@UD
     * @class Flawless
     * @since 0.0.2
     */
    final class Flawless {

      /**
       * Flawless core version.
       *
       * @static
       * @property $version
       * @type {Object}
       */
      public static $version = '0.1.1';

      /**
       * Persistent settings.
       *
       * @private
       * @property $_settings
       * @type {Object}
       */
      private $_settings = stdClass;

      /**
       * Runtime-computed settings and variables.
       *
       * @private
       * @property $_computed
       * @type {Object}
       */
      private $_computed = stdClass;

      /**
       * Reference of loaded classes, libraries and modules.
       *
       * @private
       * @property $_loaded
       * @type {Object}
       */
      private $_loaded = stdClass;

      /**
       * Content data structure.
       *
       * @private
       * @property $_structure
       * @type {Object}
       */
      private $_structure = stdClass;

      /**
       * Reference to the Flawless singleton.
       *
       * @private
       * @static
       * @property $instance
       * @type {Object}
       */
      private static $instance;

      /**
       * Initializes Theme
       *
       * Sets up global $flawless variable, loads defaults and binds primary actions.
       *
       *      var $instance = new Flawless();
       *      $instance->Loader = require( 'core/controllers/flawless/loader.php' );
       *
       * @method __construct
       * @for Flawless
       * @constructor
       *
       * @version  0.0.6
       *
       * @author potanin@UD
       * @return \Flawless\Flawless
       */
      public function __construct() {

        // Save context reference.
        self::$instance = & $this;

        define( 'Flawless_Core_Version', self::$version ); // This Version
        define( 'Flawless_Option_Key', 'settings::' . Flawless_Core_Version ); // Option Key for this version's settings.
        define( 'Flawless_Directory', basename( TEMPLATEPATH ) ); // Get Directory name
        define( 'Flawless_Path', untrailingslashit( get_template_directory() ) ); // Path for Includes
        define( 'Flawless_URL', untrailingslashit( get_template_directory_uri() ) ); // Path for front-end links
        define( 'Flawless_Transdomain', 'flawless' ); // Locale slug

        // Define Paths.
        $this->paths = (object) array(
          'controllers' => untrailingslashit( get_template_directory() ) . DIRECTORY_SEPARATOR . 'core/controllers',
          'modules'     => untrailingslashit( get_template_directory() ) . '/core/modules',
          'extend'      => untrailingslashit( get_template_directory() ) . '/core/extend',
          'helpers'     => untrailingslashit( get_template_directory() ) . '/core/helpers',
          'vendor'      => untrailingslashit( get_template_directory() ) . '/core/vendor',
          'models'      => untrailingslashit( get_template_directory() ) . '/static/models',
          'schemas'     => untrailingslashit( get_template_directory() ) . '/static/schemas',
          'templates'   => untrailingslashit( get_template_directory() ) . '/templates',
          'fonts'       => untrailingslashit( get_template_directory() ) . '/ux/styles',
          'images'      => untrailingslashit( get_template_directory() ) . '/ux/styles',
          'views'       => untrailingslashit( get_template_directory() ) . '/ux/views',
          'scripts'     => untrailingslashit( get_template_directory() ) . '/ux/scripts'
        );

        // Get Loader Class.
        require_once( $this->paths->controllers . '/loader.php' );

        // Load Controllers, Modules, Helpers and Schemas.
        new Loader( array(
          'controllers' => array(
            'Flawless\\'          => array( $this->paths->controllers ),
            'UsabilityDynamics\\' => array( $this->paths->vendor ),
            'JsonSchema\\'        => array( $this->paths->vendor . '/justinrainbow' )
          ),
          'modules'     => array(
            'modules' => $this->paths->modules,
            'extend'  => $this->paths->extend
          ),
          'helpers'     => array(
            'helpders' => $this->paths->helpers . '/template.php'
          ),
          'schemas'     => array(
            'settings' => $this->paths->schemas . '/settings.json',
            'features' => $this->paths->schemas . '/features.json',
            'headers'  => $this->paths->schemas . '/headers.json',
            'state'    => $this->paths->schemas . '/state.json'
          )
        ) );

        // Controllers.
        $this->API      = new API();
        $this->Content  = new Content();
        $this->Settings = new Settings();
        $this->Legacy   = new Legacy();
        $this->Loader   = new Loader();
        $this->Utility  = new Utility();
        $this->Theme    = new Theme();
        $this->Views    = new Views();
        $this->Log      = new Log();

        // Classses.
        // $this->Asset        = new Asset();
        // $this->Element      = new Element();
        // $this->Module       = new Module();
        // $this->Schema       = new Schema();
        // $this->Shortcode    = new Shortcode();
        // $this->Widget       = new Widget();

        // Properties.
        $this->_settings  = null;
        $this->_computed  = null;
        $this->_structure = null;
        $this->_loaded    = null;

        // Load default Flawless settings and configurations.
        $this->_settings = array(
          'have_static_home'  => ( get_option( 'show_on_front' ) == 'page' ? true : false ),
          'using_permalinks'  => ( get_option( 'permalink_structure' ) != '' ? true : false ),
          'have_blog_home'    => get_option( 'show_on_front' ) == 'page' || get_option( 'page_for_posts' ) ? true : false,
          'protocol'          => ( is_ssl() ? 'https://' : 'http://' ),
          'paths'             => $this->paths,
          'asset_directories' => apply_filters( 'flawless::asset_locations', array(
            untrailingslashit( get_template_directory() )   => untrailingslashit( get_template_directory_uri() ),
            untrailingslashit( get_stylesheet_directory() ) => untrailingslashit( get_stylesheet_directory_uri() )
          ) ),
          'theme_data'        => Loader::get_file_data( is_child_theme() ? untrailingslashit( get_stylesheet_directory() ) . '/style.css' : TEMPLATEPATH . '/style.css' )
        );

        // Earliest available callback.
        do_action( 'flawless::loaded', $this );

        // Setup Primary Actions.
        add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
        add_action( 'init', array( $this, 'init_upper' ), 0 );
        add_action( 'init', array( $this, 'init_lower' ), 500 );

      }

      /**
       * Setups up core theme functions.
       *
       * Adds image header section and default headers.
       *
       * @method after_setup_theme
       * @for Flawless
       *
       * @action after_setup_theme ( 10 )
       * @since 0.0.2
       */
      public function after_setup_theme() {
        Log::add( 'Executed: Flawless::after_setup_theme();' );
        do_action( 'flawless::theme_setup', $this );
        do_action( 'flawless::theme_setup::after', $this );
      }

      /**
       * Run on init hook, loads all other hooks and filters
       *
       * Ran as early as possible, before:
       * - widgets_init ( 1 )
       *
       * @method init_upper
       * @for Flawless
       *
       * @since 0.0.2
       */
      public function init_upper() {
        Log::add( 'Executed: Flawless::init_upper();' );

        // JavaScript Assets
        wp_register_script( 'require', '//cdnjs.cloudflare.com/ajax/libs/require.js/2.1.8/require.min.js', array(), '2.1.8', true );
        wp_register_script( 'twitter-bootstrap', '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.0/js/bootstrap.min.js', array( 'jquery' ), '3.0.0', true );

        wp_register_script( 'knockout', '//cdnjs.cloudflare.com/ajax/libs/knockout/2.3.0/knockout-min.js', array( 'jquery' ), '2.0.0', true );
        wp_register_script( 'knockout-mapping', '//cdnjs.cloudflare.com/ajax/libs/knockout.mapping/2.3.5/knockout.mapping.min.js', array( 'jquery', 'knockout' ), '2.3.5', true );
        wp_register_script( 'knockout-bootstrap', '//cdnjs.cloudflare.com/ajax/libs/knockout-bootstrap/0.2.1/knockout-bootstrap.min.js', array( 'jquery', 'knockout', 'bootstrap' ), '0.2.1', true );

        wp_register_script( 'jquery-lazyload', '//cdnjs.cloudflare.com/ajax/libs/jquery.lazyload/1.8.4/jquery.lazyload.min.js', array( 'jquery' ), '1.8.4', true );
        wp_register_script( 'jquery-cookie', '//cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.3.1/jquery.cookie.min.js', array( 'jquery' ), '1.3.1', true );
        wp_register_script( 'jquery-touch-punch', '//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.2/jquery.ui.touch-punch.min.js', array( 'jquery' ), '0.2.2', true );
        wp_register_script( 'jquery-equalheights', '//cdn.jsdelivr.net/jquery.equalheights/1.3/jquery.equalheights.min.js', array( 'jquery' ), '1.3', true );
        wp_register_script( 'jquery-placeholder', '//cdn.jsdelivr.net/jquery.placeholder/2.0.7/jquery.placeholder.min.js', array( 'jquery' ), '2.0.7', true );
        wp_register_script( 'jquery-masonry', '//cdnjs.cloudflare.com/ajax/libs/masonry/3.1.1/masonry.pkgd.js', array( 'jquery' ), '3.1.1', true );
        wp_register_script( 'jquery-fancybox', '//cdnjs.cloudflare.com/ajax/libs/fancybox/2.0.4/jquery.fancybox.pack.js', array( 'jquery' ), '2.0.4', true );

        wp_register_script( 'flawless-core', get_bloginfo( 'template_url' ) . '/ux/scripts/flawless-core.js', array( 'twitter-bootstrap', 'require' ), Flawless_Version, true );
        wp_register_script( 'flawless-frontend', get_bloginfo( 'template_url' ) . '/ux/scripts/flawless-frontend.js', array( 'flawless-core' ), Flawless_Version, true );

        // Admin Only Actions
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_init', array( $this, 'admin_init' ) );

        // Frontend Actions
        add_action( 'template_redirect', array( $this, 'template_redirect' ), 0 );

        do_action( 'flawless::init_upper', $this );

      }

      /**
       * Run on init hook, intended to load functionality towards the end of init.  Scripts are loaded here so they can be overwritten by regular init.
       *
       * Enqueue front-end assets
       *
       * @method init_lower
       * @for Flawless
       *
       * @filter init ( 500 )
       * @since 0.0.2
       */
      public function init_lower() {
        Log::add( 'Executed: Flawless::init_lower();' );

        // Do not load these styles if we are on admin side or the WP login page.
        if ( strpos( $_SERVER[ 'SCRIPT_NAME' ], 'wp-login.php' ) ) {
          return;
        }

        add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 100 );
        add_action( 'wp_print_styles', array( $this, 'wp_print_styles' ), 100 );

        do_action( 'flawless::init_lower', $this );

      }

      /**
       * Admin Menu Handler
       *
       * @method admin_menu
       * @for Flawless
       */
      public function admin_menu() {
        Log::add( 'Executed: Flawless::admin_menu();' );
        do_action( 'flawless::admin_menu', $this );
      }

      /**
       * Displays first-time setup splash screen
       *
       *
       * @since 0.0.2
       */
      public function admin_init() {
        Log::add( 'Executed: Flawless::admin_init();' );
        do_action( 'flawless::admin_init', $this );
      }

      /**
       * Primary function for handling front-end actions
       *
       * @filter template_redirect ( 0 )
       * @since 0.0.2
       */
      public function template_redirect() {
        Log::add( 'Executed: Flawless::template_redirect();' );
        do_action( 'flawless::template_redirect', $this );
      }

      /**
       * Front-end script loading
       *
       * Loads all local and remote assets, checks conditionally loaded assets, etc.
       * Modifies body class based on loaded assets.
       *
       * @method wp_enqueue_scripts
       * @for Flawless
       *
       * @filter wp_enqueue_scripts ( 100 )
       * @since 0.0.2
       * @todo Scripts should be registered here, but enqueved at a differnet level. - potanin@UD
       */
      public function wp_enqueue_scripts() {
        Log::add( 'Executed: Flawless::wp_enqueue_scripts();' );

        do_action( 'flawless::wp_enqueue_scripts', $this );
        do_action( 'flawless::extra_local_assets', $this );

        if ( wp_is_mobile() ) {
          do_action( 'flawless::wp_enqueue_scripts::mobile', $this );
        }

        wp_enqueue_script( 'flawless-frontend' );

        do_action( 'flawless::wp_enqueue_scripts::end', $this );

      }

      /**
       * Enqueue Frontend Styles. Loaded late so plugin styles can be used for compiling and so theme CSS has more specificity.
       *
       * @method wp_print_styles
       * @for Flawless
       *
       * @filter wp_print_styles ( 100 )
       * @since 0.0.6
       */
      public function wp_print_styles() {
        Log::add( 'Executed: Flawless::wp_print_styles();' );

        // Always Enqueue core style.css.
        if ( file_exists( TEMPLATEPATH . '/ux/styles/app.css' ) ) {
          wp_enqueue_style( 'flawless-app', get_bloginfo( 'template_url' ) . '/ux/styles/app.css', array( 'flawless-bootstrap-css' ), Flawless_Version, 'all' );
        }

        // Enqueue child theme style.css.
        if ( is_child_theme() ) {
          wp_enqueue_style( 'flawless-child-app-style', get_bloginfo( 'stylesheet_directory' ) . '/ux/styles/app.css', array( 'flawless-app' ), Flawless_Version );
        }

        do_action( 'flawless::wp_print_styles', $this );

      }

      /**
       * Get Setting.
       *
       * @method get
       *
       * @for Flawless
       * @author potanin@UD
       * @since 0.1.1
       */
      public function get() {

      }

      /**
       * Set Setting.
       *
       * @method get
       * @for Flawless
       *
       * @author potanin@UD
       * @since 0.1.1
       */
      public function set() {

      }

      /**
       * Get the Flawless Singleton
       *
       * Concept based on the CodeIgniter get_instance() concept.
       *
       * @example
       *
       *      var settings = Flawless::get_instance()->Settings;
       *      var api = Flawless::$instance()->API;
       *
       * @static
       * @return object
       *
       * @method get_instance
       * @for Flawless
       *
       */
      public static function &get_instance() {
        return self::$instance;
      }

    }

    // Initialize the theme.
    new Flawless();

  }

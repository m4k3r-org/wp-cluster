<?php
/**
 *
 */
namespace UsabilityDynamics\LayoutEngine {

  if( !class_exists( 'UsabilityDynamics\LayoutEngine\Bootstrap' ) ) {

    /**
     * Class Bootstrap
     *
     * @package UsabilityDynamics\LayoutEngine
     */
    class Bootstrap {

      /**
       * Settings Class version.
       *
       * @public
       * @static
       * @property $version
       * @type {Object}
       */
      public static $version = '0.0.1';

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
       * Instantiate
       *
       * @author potanin@UD
       * @method __constrct
       * @for Bootstrap
       */
      public function __construct() {

        // Singleton.
        if( self::$instance ) {
          return self::$instance;
        }

        // Save instance..
        self::$instance = & $this;

        // Instantiate core
        // $this->_core = new Core();

        // $this->_settings      = new \UsabilityDynamics\Settings();
        // $this->_log         = new \UsabilityDynamics\Log();
        // $this->_utility     = new \UsabilityDynamics\Utility();

        // $this->set( 'vendor_path', defined( 'WP_VENDOR_PATH' ) ? WP_VENDOR_PATH : basename( __DIR__ ) );
        // $this->set( 'vendor_url', defined( 'WP_VENDOR_URL' ) ? WP_VENDOR_URL : basename( __DIR__ ) );

        // Hook into WordPress
        add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );

      }

      /**
       * Register Element
       *
       * @param bool|string $name
       * @param bool|array $options
       *
       * @return mixed
       */
      public function register( $name = false, $options = false ) {
        Core::register( $name, $options );
      }

      /**
       * Enable Feature
       *
       * @param bool|string       $feature
       * @param array|bool|object $options
       *
       * @return void
       */
      public function enable( $feature = false, $options = false ) {
        Core::enable( $feature, $options );
      }

      /**
       * Initializer after theme is ready
       *
       * @method after_setup_theme
       * @for Bootstrap
       */
      public function after_setup_theme() {

        if( is_dir( WP_VENDOR_PATH . '/usabilitydynamics/lib-carrington-build' ) ) {
          Core::enable( 'carrington', WP_VENDOR_PATH . '/usabilitydynamics/lib-carrington-build' );
        }

      }

      /**
       * Get Setting.
       *
       * @method get
       *
       * @for Bootstrap
       * @author potanin@UD
       * @since 0.1.1
       */
      static public function get( $key, $default = false ) {
        // return self::$instance->_settings->get( $key, $default );
      }

      /**
       * Set Setting.
       *
       * @method get
       * @for Bootstrap
       *
       * @author potanin@UD
       * @since 0.1.1
       */
      static public function set( $key, $value ) {
        // return self::$instance->_settings->set( $key, $value );
      }

      /**
       * Class Logger
       *
       * (Not implemented, should reference Utility::log() or something);
       *
       * @method log
       * @for Bootstrap
       *
       * @author potanin@UD
       * @since 0.1.1
       */
      static public function log( $data ) {
      }

      /**
       * Get the Bootstrap Singleton
       *
       * Concept based on the CodeIgniter get_instance() concept.
       *
       * @example
       *
       *      var settings = Bootstrap::get_instance()->Settings;
       *      var api = Bootstrap::$instance()->API;
       *
       * @static
       * @return object
       *
       * @method get_instance
       * @for Bootstrap
       *
       */
      static public function &get_instance() {
        return self::$instance;
      }

    }

  }

}
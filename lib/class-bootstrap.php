<?php
/**
 * Bootstrap
 *
 */
namespace DiscoDonniePresents\Eventbrite {

  use UsabilityDynamics\Settings;

  if( !class_exists( 'DiscoDonniePresents\Eventbrite\Bootstrap' ) ) {

    class Bootstrap {

      /**
       * Core version.
       *
       * @static
       * @property $version
       * @type {Object}
       */
      public $version = false;

      /**
       * Textdomain String
       *
       * @public
       * @property domain
       * @var string
       */
      public $domain = false;

      /**
       * Singleton Instance Reference.
       *
       * @public
       * @static
       * @property $instance
       * @type {Object}
       */
      private static $instance = null;

      public $option = array();

      /**
       * Instantaite class.
       */
      private function __construct() {

        $plugin_data = get_file_data( ( dirname( __DIR__ ) . '/wp-eventbrite.php' ), array(
          'Name' => 'Plugin Name',
          'Version' => 'Version',
          'TextDomain' => 'Text Domain',
        ), 'plugin' );

        $this->version  = trim( $plugin_data[ 'Version' ] );
        $this->domain   = trim( $plugin_data[ 'TextDomain' ] );

        //** Init Settings */
        $this->settings = $this->defineSettings();
        
        //** Load Core on 'after_setup_theme' */
        add_action( 'after_setup_theme', array( $this, 'load' ) );
        
      }
      
      /**
       * Loads Plugin's functionality
       *
       * @action after_setup_theme
       * @author peshkov@UD
       */
      public function load() {
        new \DiscoDonniePresents\Eventbrite\Core();
      }
      
      /**
       * Determine if instance already exists and Return Theme Instance
       *
       */
      public static function get_instance( $args = array() ) {
        return null === self::$instance ? self::$instance = new self() : self::$instance;
      }

      /**
       * @param null $key
       * @param null $value
       *
       * @return \UsabilityDynamics\Settings
       */
      public function set( $key = null, $value = null ) {
        return $this->settings->set( $key, $value );
      }

      /**
       * @param null $key
       * @param null $default
       *
       * @return \UsabilityDynamics\type
       */
      public function get( $key = null, $default = null ) {
        return $this->settings->get( $key, $default );
      }
      
      /**
       * Initializes and returns Settings object
       * 
       * @return object UsabilityDynamics\Settings
       */
      private function defineSettings() {
        //** Initialize Settings. */
        $settings = new \UsabilityDynamics\Settings( array(
          'key'  => 'wp_eventbrite',
          'store'  => 'options'
        ));
        //** Merge with default data. */
        $data = \UsabilityDynamics\Utility::extend( Utility::get_schema( 'default.settings' ), $settings->get(), array(
          'version' => $this->version,
          'domain' => $this->domain,
        ) );
        if( !empty( $data ) ) {
          $settings->set( $data );
        }
        // Return Instance.
        return $settings;
      }

    }

  }

}

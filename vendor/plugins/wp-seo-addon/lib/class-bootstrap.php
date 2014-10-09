<?php
/**
 *
 *
 */
namespace UsabilityDynamics\SEO {

  if( !class_exists( '\UsabilityDynamics\SEO\Bootstrap' ) ) {

    class Bootstrap {

      /**
       * Cluster core version.
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
       * @property text_domain
       * @var string
       */
      public $text_domain = false;

      /**
       *
       */
      public $seo = NULL;
      
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
       *
       */
      private function __construct() {
        
        $plugin_data = \get_file_data( ( dirname( __DIR__ ) . '/wp-seo-addon.php' ), array(
          'Name' => 'Plugin Name',
          'Version' => 'Version',
          'TextDomain' => 'Text Domain',
        ), 'plugin' );
        
        $this->version = trim( $plugin_data['Version'] );
        $this->text_domain = trim( $plugin_data['TextDomain'] );
        
        $this->settings = new \UsabilityDynamics\Settings( array(
          'key'  => $this->text_domain,
          'data' => array(
            'version' => $this->version,
            'text_domain' => $this->text_domain,
          )
        ) );
        
        //** Run plugin on after_setup_theme hook */
        add_action( "after_setup_theme", array( $this, 'run' ) );

      }
      
      /**
       *
       */
      public function run() {
        if( defined( 'WPSEO_VERSION' ) && version_compare( WPSEO_VERSION, '1.5.3.3' ) >= 0 ) {
          //** Adds Social Twitter customizations */
          new Twitter();
          //** Adds Sitewide Custom Meta Functionality */
          new Custom_Meta();
        }
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

    }

  }

}

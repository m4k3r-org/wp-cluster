<?php
/**
 *
 *
 */
namespace UsabilityDynamics\AMD {

  if( !class_exists( '\UsabilityDynamics\AMD\Bootstrap' ) ) {

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
       * Singleton Instance Reference.
       *
       * @public
       * @static
       * @property $instance
       * @type {Object}
       */
      public static $instance = null;

      public $option = array();

      /**
       *
       */
      private function __construct() {
        
        $plugin_data = \get_file_data( ( dirname( __DIR__ ) . '/wp-amd.php' ), array(
          'Name' => 'Plugin Name',
          'Version' => 'Version',
          'TextDomain' => 'Text Domain',
        ), 'plugin' );
        
        $this->version = trim( $plugin_data['Version'] );
        $this->text_domain = trim( $plugin_data['TextDomain'] );
        
        $this->settings = new \UsabilityDynamics\Settings( array(
          'key'  => 'wp_amd',
          'data' => array(
            'version' => $this->version,
            'text_domain' => $this->text_domain,
            'assets' => array(
              'script' => array(
                'type' => 'script',
                'minify' => false,
                'admin_menu' => true,
                'load_in_head' => false,
                'permalink' => "assets/wp-amd.js",
                'dependencies' => array(
                  'jquery' => array(
                    'name'         => 'jQuery',
                    'infourl'      => 'http://jquery.com',
                    'url'          => 'http://code.jquery.com/ui/1.10.4/jquery-ui.min.js',
                  ),
                  'jquery-ui-autocomplete' => array(
                    'name'         => 'jQuery UI Autocomplete',
                    'infourl'      => 'http://jqueryui.com/autocomplete',
                  ),
                  'backbone'  => array(
                    'name'         => 'Backbone js',
                    'infourl'      => 'http://backbonejs.com',
                    'url'          => 'http://backbonejs.org/backbone-min.js',
                  ),
                  'json2' => array(
                    'name'         => 'JSON for JS',
                    'infourl'      => 'https://github.com/douglascrockford/JSON-js',
                  ),
                  'thickbox' => array(
                    'name'         => 'Thickbox',
                    'infourl'      => 'http://codex.wordpress.org/ThickBox',
                  ),
                  'underscore' => array(
                    'name'         => 'Underscore js',
                    'infourl'      => 'http://underscorejs.org',
                    'url'          => 'http://underscorejs.org/underscore-min.js',
                  )
                ),
              ),
              'style'  => array(
                'type' => 'style',
                'minify' => false,
                'permalink' => "assets/wp-amd.css",
                'admin_menu' => true,
                'load_in_head' => true,
                'dependencies' => array(),
              ),
            ),
          )
        ) );
        
        //** Init our scripts and styles classes */
        $this->style = new Style( $this->get( 'assets.style' ) );
        $this->script = new Script( $this->get( 'assets.script' ) );
        
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

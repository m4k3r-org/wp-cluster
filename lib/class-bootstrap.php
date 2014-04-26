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
                'permalink' => apply_filters( 'wp-amd:script:path', 'assets/wp-amd.js', $this ),
                'dependencies' => apply_filters( 'wp-amd:script:dependencies', array(
                  'jquery-ui-autocomplete' => array(
                    'name'         => 'jQuery UI Autocomplete',
                    'infourl'      => 'http://jqueryui.com/autocomplete',
                    'dependencies' => array( 'jquery' )
                  ),
                  'backbone'  => array(
                    'name'         => 'Backbone.js',
                    'infourl'      => 'http://backbonejs.com',
                    'url'          => 'http://backbonejs.org/backbone-min.js',
                  ),
                  'lodash' => array(
                    'name'         => 'Lodash',
                    'infourl'      => 'http://lodash.com',
                    'version'      => '2.4.1',
                    'url'          => 'http://cdnjs.cloudflare.com/ajax/libs/lodash.js/2.4.1/lodash.js',
                  ),
                  'knockout' => array(
                    'name'         => 'Knockout.js',
                    'version'      => '2.2.1',
                    'infourl'      => 'http://knockoutjs.com',
                    'url'          => 'http://ajax.aspnetcdn.com/ajax/knockout/knockout-2.2.1.js',
                  ),
                  'udx.requires' => array(
                    'name'         => 'UDX.Requires',
                    'infourl'      => 'http://cdn.udx.io',
                    'url'          => 'http://cdn.udx.io/udx.requires.js',
                  )
                ), $this ),
              ),
              'style'  => array(
                'type' => 'style',
                'minify' => false,
                'permalink' => apply_filters( 'wp-amd:style:path', 'assets/wp-amd.css', $this ),
                'admin_menu' => true,
                'load_in_head' => true,
                'dependencies' => apply_filters( 'wp-amd:style:dependencies', array(), $this ),
              )
            )
          )
        ));
        
        //** Init our scripts and styles classes */
        $this->style    = new Style( $this->get( 'assets.style' ) );
        $this->script   = new Script( $this->get( 'assets.script' ) );
        
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

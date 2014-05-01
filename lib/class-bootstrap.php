<?php
/**
 *
 *
 */
namespace UsabilityDynamics\AMD {

  use UsabilityDynamics\Settings;

  if( !class_exists( 'UsabilityDynamics\AMD\Bootstrap' ) ) {

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
      public static $instance = null;

      public $option = array();

      /**
       *
       */
      private function __construct() {
        
        $plugin_data = get_file_data( ( dirname( __DIR__ ) . '/wp-amd.php' ), array(
          'Name' => 'Plugin Name',
          'Version' => 'Version',
          'TextDomain' => 'Text Domain',
        ), 'plugin' );
        
        $this->version = trim( $plugin_data['Version'] );
        $this->domain = trim( $plugin_data['TextDomain'] );
        
        $this->settings = new Settings( array(
          'key'  => 'wp_amd',
          'data' => array(
            'version' => $this->version,
            'domain' => $this->domain,
            'assets' => array(
              'script' => array(
                'type'          => 'script',
                'disk_cache'    => apply_filters( 'wp-amd:script:disk_cache', true, $this ),
                'minify'        => false,
                'admin_menu'    => true,
                'load_in_head'  => false,
                'permalink'     => apply_filters( 'wp-amd:script:path', 'assets/wp-amd.js', $this ),
                'dependencies'  => apply_filters( 'wp-amd:script:dependencies', array(
                  'jquery'  => array(
                    'id'            => 'jquery',
                    'name'          => 'jQuery',
                    'infourl'       => 'http://jquery.com',
                    'version'       => '2.1.0',
                    'url'           => '//ajax.aspnetcdn.com/ajax/jquery/jquery-2.1.0.min.js',
                  ),
                  'backbone'  => array(
                    'id'            => 'backbone',
                    'name'          => 'Backbone.js',
                    'infourl'       => 'http://backbonejs.com',
                    'url'           => 'http://backbonejs.org/backbone-min.js',
                  ),
                  'lodash' => array(
                    'id'            => 'lodash',
                    'name'          => 'Lodash',
                    'infourl'       => 'http://lodash.com',
                    'version'       => '2.4.1',
                    'url'           => 'http://cdnjs.cloudflare.com/ajax/libs/lodash.js/2.4.1/lodash.js',
                  ),
                  'knockout' => array(
                    'id'            => 'knockout',
                    'name'          => 'Knockout.js',
                    'version'       => '2.2.1',
                    'infourl'       => 'http://knockoutjs.com',
                    'url'           => 'http://ajax.aspnetcdn.com/ajax/knockout/knockout-2.2.1.js',
                  ),
                  'udx-requires' => array(
                    'id'            => 'udx-requires',
                    'name'          => 'Requires.js (UDX)',
                    'infourl'       => 'http://cdn.udx.io',
                    'version'       => '4.0.0',
                    'url'           => '//cdn.udx.io/udx.requires.js',
                  ),
                  'twitter-bootstrap' => array(
                    'id'            => 'twitter-bootstrap',
                    'name'          => 'Twitter Bootstrap',
                    'infourl'       => 'http://getbootstrap.com/',
                    'version'       => '3.1.1',
                    'url'           => '//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js'
                  ),
                  'google-analytics' => array(
                    'id'            => 'google-analytics',
                    'name'          => 'Google Analytics',
                    'infourl'       => 'http://google-analytics.com/',
                    'version'       => '3.1.1',
                    'url'           => '//www.google-analytics.com/urchin.js'
                  )
                ), $this ),
              ),
              'style'  => array(
                'type'          => 'style',
                'minify'        => false,
                'disk_cache'    => apply_filters( 'wp-amd:script:disk_cache', true, $this ),
                'permalink'     => apply_filters( 'wp-amd:style:path', 'assets/wp-amd.css', $this ),
                'admin_menu'    => true,
                'load_in_head'  => true,
                'dependencies'  => apply_filters( 'wp-amd:style:dependencies', array(
                  'twitter-bootstrap' => array(
                    'id'            => 'twitter-bootstrap',
                    'name'          => 'Twitter Bootstrap',
                    'version'       => '3.1.1',
                    'infourl'       => 'http://getbootstrap.com/',
                    'url'           => '//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css'
                  ),
                  'normalize' => array(
                    'id'            => 'normalize',
                    'name'          => 'Normalize',
                    'version'       => '3.0.1',
                    'infourl'       => 'http://getbootstrap.com/',
                    'url'           => '//cdnjs.cloudflare.com/ajax/libs/normalize/3.0.1/normalize.min.css'
                  ),
                  'fontawesome' => array(
                    'id'            => 'fontawesome',
                    'name'          => 'Font Awesome',
                    'infourl'       => 'http://fontawesome.io/',
                    'url'           => '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.min.css'
                  ),
                ), $this ),
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

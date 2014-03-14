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
            'javascript' => array(
              'type' => 'script',
              'minify' => false,
              'permalink' => "assets/app.js",
              'dependencies' => array(
                'backbone'  => array(
                  'name'         => 'Backbone js',
                  'load_in_head' => false,
                  'infourl'      => 'http://backbonejs.com'
                ),
                'jquery' => array(
                  'name'         => 'jQuery',
                  'load_in_head' => false,
                  'infourl'      => 'http://jquery.com'
                ),
                'jquery-ui-autocomplete' => array(
                  'name'         => 'jQuery UI Autocomplete',
                  'load_in_head' => false,
                  'infourl'      => 'http://jqueryui.com/autocomplete'
                ),
                'json2' => array(
                  'name'         => 'JSON for JS',
                  'load_in_head' => false,
                  'infourl'      => 'https://github.com/douglascrockford/JSON-js'
                ),
                'thickbox' => array(
                  'name'         => 'Thickbox',
                  'load_in_head' => false,
                  'infourl'      => 'http://codex.wordpress.org/ThickBox'
                ),
                'underscore' => array(
                  'name' => 'Underscore js',
                  'load_in_head' => false,
                  'infourl' => 'http://underscorejs.org'
                )
              ),
            ),
            'stylesheet'  => array(
              'type' => 'style',
              'minify' => false,
              'permalink' => "assets/app.css",
              'dependencies' => array(),
            ),
          )
        ) );
        
        //** Init our scripts and styles classes */
        //$this->style = new Style( $this->get( 'stylesheet' ) );
        $this->script = new Script( $this->get( 'javascript' ) );
        
      }

      /**
       * Determine if instance already exists and Return Theme Instance
       *
       */
      public static function get_instance( $args = array() ) {
        return null === self::$instance ? self::$instance = new self() : self::$instance;
      }

      /**
       * Get latest revision ID
       * @return string
       */
      public function get_latest_version_id( $post_id ) {
        if( $a = array_shift( get_posts( array( 'numberposts' => 1, 'post_type' => 'revision', 'post_status' => 'any', 'post_parent' => $post_id ) ) ) ) {
          $post_row = get_object_vars( $a );
          return $post_row[ 'ID' ];
        }
        return 'unknown';
      }

      /**
       * get_plugin_post_id function
       * Gets the post id from posts table
       *
       * @access public
       * @return bool $post_id
       */
      public function get_plugin_post_id( $type ) {
        if( $a = array_shift( get_posts( array( 'numberposts' => 1, 'post_type' => 's-global-'.$type, 'post_status' => 'publish' ) ) ) ):
          $post_row = get_object_vars( $a );

          return $post_row[ 'ID' ];
        else:
          return false;
        endif;
      }

      /**
       * get_saved_dependencies function
       *
       * @access public
       *
       * @param $post_id
       *
       * @return array|mixed $dependency_arr
       */
      function get_saved_dependencies( $post_id ) {
        $dependency_arr = get_post_meta( $post_id, 'dependency', true );
        if( !is_array( $dependency_arr ) )
          $dependency_arr = array();

        return $dependency_arr;
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

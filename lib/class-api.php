<?php
/**
 * REST API Controller
 *
 * @module Veneer
 * @author potanin@UD
 */
namespace UsabilityDynamics\Veneer {
  use \UsabilityDynamics\Utility as Utility;

  if( !class_exists( 'UsabilityDynamics\Veneer\API' ) ) {

    /**
     * Class API
     *
     * @module Veneer
     */
    class API extends \UsabilityDynamics\API {

      /** Setup our namespace */
      public static $namespace = 'wp-veneer';

      /**
       * Initialize API
       *
       * @version 0.1.5
       * @for API
       */
      public function __construct() {
        if( is_callable( array( 'parent', '__construct' ) ) ) {
          parent::__construct();
        }
        /** Let's go ahead and add our routes */
        add_action( 'plugins_loaded', array( $this, 'create_routes' ));

        /** Return this */

        return $this;
      }

      /**
       * This function lets us chain methods without having to instantiate first, YOU MUST COPY THIS TO ALL SUB CLASSES
       */
      static public function init() {
        return new self( __DIR__, false );
      }

      /**
       * Creates our routes
       */
      public function create_routes() {

        if( is_callable( array( 'parent', 'create_routes' ) ) ) {
          parent::create_routes();
        }

        /** @url http://{domain}/wp-veneer/api/v1/migrate */
        self::define( '/system/v1/migrate', array(
          'parameters' => array( 'name', 'version' ),
          'scopes'     => array( 'install_plugins', 'activate_plugins' ),
          'handler'    => array( $this, 'migrateSite' )
        ));

        /** @url http://{domain}/wp-veneer/api/v1/install/plugin */
        self::define( '/system/v1/install', array(
          'parameters' => array( 'name', 'version' ),
          'scopes'     => array( 'install_plugins', 'activate_plugins' ),
          'handler'    => array( $this, 'installPlugin' )
        ));

      }

      /**
       * Install a specific plugin
       */
      public function installPlugin() {
        
        /** Setup our args */
        $args = (array) Utility::parse_args( $_GET, array(
          'name'    => '',
          'version' => ''
        ));
        
        /** Call our function to install the plugins */
        // $return = call_user_func_array( array( Veneer::get_instance()->_plugins, 'installPlugin' ), array( $args ));
        
        /** @todo WIP! */
        self::send( array(
          'ok'      => false,
          'message' => 'This function needs to be completed.'
        ));
        
      }

      /**
       * Perform System Migration
       */
      static public function migrateSite() {
        global $wpdb, $current_site, $current_blog;

        if( !current_user_can( 'manage_options' ) ) {
          wp_send_json( array( 'ok' => false, 'message' => __( 'Invalid capabilities.' ) ));

          return;
        }

        $activePlugins = array();

        $_results = array();

        delete_site_transient( 'update_plugins' );

        foreach( $activePlugins as $plugin ) {
          $_results[ $plugin ] = Utility::install_plugin( $plugin );
        }

        // Remove the stupid "upgrade" directory.
        if( is_dir( $_SERVER[ 'DOCUMENT_ROOT' ] . '/upgrade' ) ) {
          @rmdir( $_SERVER[ 'DOCUMENT_ROOT' ] . '/upgrade' );
        }

        // Bail out
        self::send( array(
          'message' => __( 'Sstem upgrade ran.' ),
          'results' => $_results
        ));

      }

    }

  }

}
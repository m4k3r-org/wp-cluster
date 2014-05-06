<?php
/**
 *
 * @author Andy Potanin <andy.potanin@usabilitydynamics.com>
 */
namespace wpCloud\Vertical\EDM {

  class Bootstrap {

    function initialize() {

      // Define API Endpoints.
      API::define( '/artists',           array( 'wpCloud\Vertical\API',     'getArtists' ) );
      API::define( '/artist',            array( 'wpCloud\Vertical\API',     'getArtist' ) );
      API::define( '/venues',            array( 'wpCloud\Vertical\API',     'getVenues' ) );
      API::define( '/venue',             array( 'wpCloud\Vertical\API',     'getVenue' ) );
      API::define( '/system/upgrade',    array( 'wpCloud\Vertical\API',     'systemUpgrade' ) );

      add_action( 'network_admin_menu', 'wpCloud\Modules\Intelligence::admin_menu', 20 );

      // register_theme_directory( WP_CONTENT_DIR . '/themes' );
      // register_theme_directory( WP_VENDOR_PATH . '/themes' );

      add_action( 'login_enqueue_scripts', array( $this, 'login_enqueue_scripts' ), 30 );
      // add_action( 'login_footer', array( &$this, 'login_footer' ), 30 );
      add_action( 'upload_mimes', array( &$this, 'upload_mimes' ), 100 );
      // add_action( 'muplugins_loaded', array( &$this, 'muplugins_loaded' ), 0 );
      // add_action( 'plugins_loaded', array( &$this, 'plugins_loaded' ) );

      add_filter( 'wp_cache_themes_persistently', function( $current, $callee ) { return 43200;  }, 10, 2);

      if( class_exists( '\UsabilityDynamics\Utility' ) ) {
        add_filter( 'sanitize_file_name', array( '\UsabilityDynamics\Utility', 'hashify_file_name' ), 10 );
      }

    }

  }

}
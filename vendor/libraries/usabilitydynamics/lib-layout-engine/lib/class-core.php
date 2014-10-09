<?php
/**
 * LayoutEngine Core
 *
 * @version 0.0.1
 * @author potanin@UD
 * @namespace UsabilityDynamics\LayoutEngine
 */
namespace UsabilityDynamics\LayoutEngine {

  if( !class_exists( 'UsabilityDynamics\LayoutEngine\Core' ) ) {

    /**
     * Class Core
     *
     * @class Core
     * @package UsabilityDynamics\LayoutEngine
     */
    class Core {

      /**
       * Instantiate
       *
       * @author potanin@UD
       * @method __constrct
       * @for Core
       */
      public function __construct() {

        //$this->enable = $this->enable;
        //$this->register = $this->register;

      }

      /**
       * @param        $type
       * @param string $classname
       * @param array  $args
       *
       * @return bool
       */
      public function register( $type, $classname = '', $args = array( )) {
        global $cfct_build;

        switch ($type) {

          case 'module':
            return $cfct_build->template->register_type( 'module', $classname, $args );
          break;

          case 'row':
            return $cfct_build->template->register_type( 'row', $classname );
          break;

        }

        return false;

      }

      /**
       * @param        $type
       * @param string $classname
       *
       * @return bool
       */
      public function deregister( $type, $classname = '') {
        global $cfct_build;

        switch ($type) {

          case 'module':
            return $cfct_build->template->deregister_type( 'module', $classname, $args );
          break;

          case 'row':
            return $cfct_build->template->deregister_type( 'row', $classname );
          break;

        }

        return false;

      }

      /**
       * Enable Carrington Build
       *
       * Filters
       * - cfct-modern-widgets
       * - cfct-module-dirs
       * - cfct-module-option-dirs
       * - cfct-row-dirs
       * - cfct-module-extras
       * - cfct-row-extras
       * - cfct-enable-custom-attributes
       * - cfct-build-module-url-unknown
       * - cfct-build-module-url
       *
       * @param string $feature
       * @param bool   $options
       * @param bool   $path
       */
      public function enable( $feature = '', $options = false, $path = false ) {

        if( !$path && is_string( $options ) ) {
          $path = $options;
        }

        if( $feature == 'carrington' ) {

          add_filter( 'cfct-build-module-url', array( 'UsabilityDynamics\LayoutEngine\Module', 'public_url' ), 10, 3 );
          add_filter( 'cfct-build-module-urls', array( 'UsabilityDynamics\LayoutEngine\Module', 'module_urls' ) );

          // Set CB Path
          add_filter( 'cfct-build-dir', function () {
            return WP_VENDOR_PATH . '/usabilitydynamics/lib-carrington-build/lib/';
          } );

          // Set CB URL
          add_filter( 'cfct-build-url', function () {
            return WP_VENDOR_URL . '/usabilitydynamics/lib-carrington-build/lib/';
          } );

          // Remove CB JS and CSS
          add_filter( 'init', function () {
            wp_dequeue_script( 'cfct-build-js' );
            wp_dequeue_script( 'cfct-admin-js' );
            wp_dequeue_style( 'cfct-admin-css' );
            wp_dequeue_style( 'cfct-build-css' );
          }, 100 );

          // Constants
          @define( 'CFCT_BUILD_TAXONOMY_LANDING', false );
          @define( 'CFCT_BUILD_DEBUG', false );
          @define( 'CFCT_BUILD_DEBUG_ERROR_LOG', false );
          @define( 'CFCT_BUILD_DEBUG_DISPLAY_ERRORS', false );

          include( $path . '/lib/carrington-build.php' );

        }

      }

    }

  }

}
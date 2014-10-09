<?php
/**
 * LayoutEngine Module
 *
 * @version 0.0.1
 * @author potanin@UD
 * @namespace UsabilityDynamics\LayoutEngine
 */
namespace UsabilityDynamics\LayoutEngine {

  if( !class_exists( 'UsabilityDynamics\LayoutEngine\Module' ) ) {

    /**
     * Class Module
     *
     * @class Module
     * @package UsabilityDynamics\LayoutEngine
     */
    class Module {

      /**
       * Instantiate
       *
       * @author potanin@UD
       * @method __constrct
       * @for Module
       */
      public function __construct() {

      }

      /**
       * Load module data from module file. Headers differ from WordPress
       * plugin headers to avoid them being identified as standalone
       * plugins on the WordPress plugins page.
       */
      public static function get_module( $module = '' ) {

        if ( !file_exists( $module ) && !is_dir( $module ) ) {
          return false;
        }

        // JetPack Headers.
        $mod = get_file_data( $module, array(
          'name'                => 'Module Name',
          'description'         => 'Module Description',
          'sort'                => 'Sort Order',
          'introduced'          => 'First Introduced',
          'changed'             => 'Major Changes In',
          'deactivate'          => 'Deactivate',
          'free'                => 'Free',
          'requires_connection' => 'Requires Connection',
          'auto_activate'       => 'Auto Activate',
        ));

        return $mod;

      }

      public function module_urls( $modules = array() ) {
        return $modules;
      }

      /**
       * Identify Module URL
       *
       * @param $url
       * @param $module
       * @param $file_key
       *
       * @return string
       */
      public function public_url( $url, $module, $file_key ) {

        if( strpos( $module, WP_VENDOR_PATH ) >= 0 ) {
          return trailingslashit( dirname( str_replace( WP_VENDOR_PATH, WP_VENDOR_URL, $module ) ) );
        }

        if( !$url ) {
          trigger_error( __( 'Unable to identify module URL of a module.' ) );
        }

        return $url;

      }

    }

  }
}
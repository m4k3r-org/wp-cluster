<?php
/**
 *
 *
 * @module Cluster
 * @author potanin@UD
 */
namespace UsabilityDynamics\Cluster {

  if( !class_exists( 'UsabilityDynamics\Cluster\Debug' ) ) {

    /**
     * Class Debug
     *
     * @module Cluster
     */
    class Debug {

      /**
       * Initialize Debug
       *
       * @for Debug
       */
      public function __construct() {

        // Overwrite default logs location if a custom location is configured
        if( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG && defined( 'WP_LOGS_DIR' ) && is_writable( WP_LOGS_DIR ) ) {
          ini_set( 'log_errors', 1 );
          error_reporting( E_ALL ^ E_NOTICE );
          ini_set( 'error_log', WP_LOGS_DIR . '/' . date( 'Y-m-d' ) . '.log' );
        }

      }

    }

  }

}
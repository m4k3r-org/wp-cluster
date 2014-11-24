<?php
/**
 * Locale
 *
 * @module Cluster
 * @author potanin@UD
 */
namespace UsabilityDynamics\Cluster {

  if( !class_exists( 'UsabilityDynamics\Cluster\Locale' ) ) {

    /**
     * Class Locale
     *
     * @module Cluster
     */
    class Locale {

      /**
       * Initialize Locale
       *
       * @for Locale
       */
      public function __construct() {

        // Language
        if( defined( 'WP_LANG_DIR' ) && is_readable( WP_LANG_DIR ) ) {
        }

      }

    }

  }
}

<?php
/**
 * Locale
 *
 * @module Veneer
 * @author potanin@UD
 */
namespace UsabilityDynamics\Veneer {

  if( !class_exists( 'UsabilityDynamics\Veneer\Locale' ) ) {

    /**
     * Class Locale
     *
     * @module Veneer
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

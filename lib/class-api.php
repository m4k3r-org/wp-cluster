<?php
/**
 * API Access Controller
 *
 * @version 0.1.5
 * @module Veneer
 * @author potanin@UD
 */
namespace UsabilityDynamics\Veneer {

  if( !class_exists( 'UsabilityDynamics\Veneer\API' ) ) {

    /**
     * Class API
     *
     * @module Veneer
     */
    class API {

      /**
       * Initialize API
       *
       * @version 0.1.5
       * @for API
       */
      public function __construct() {

        $this->actual_url = admin_url( 'admin-ajax.php' );
      }

    }
  }
}
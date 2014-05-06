<?php
/**
 * API Access Controller
 *
 * @version 0.1.5
 * @module Cluster
 * @author potanin@UD
 */
namespace UsabilityDynamics\Cluster {

  if( !class_exists( 'UsabilityDynamics\Cluster\API' ) ) {

    /**
     * Class API
     *
     * @module Cluster
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
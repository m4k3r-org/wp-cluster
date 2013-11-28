<?php
/**
 * Veneer Job
 *
 * @module Veneer
 * @author potanin@UD
 */
namespace UsabilityDynamics\Veneer {

  if( !class_exists( 'UsabilityDynamics\Veneer\Job' ) ) {

    /**
     * Class Job
     *
     * @extends UsabilityDynamics\RaaS\Job
     * @class Job
     * @module Veneer
     */
    class Job extends \UsabilityDynamics\RaaS\Job {

      /**
       * Initialize JOb
       *
       * @for Job
       */
      public function __construct( $settings ) {

        // Instantaite UsabilityDynamics/RaaS/Job
        parent::__construct( $settings );

        //return $this;

      }

    }

  }

}
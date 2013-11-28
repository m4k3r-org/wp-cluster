<?php
/**
 * Veneer Jobs
 *
 * @module Veneer
 * @author potanin@UD
 */
namespace UsabilityDynamics\Veneer {

  if( !class_exists( 'UsabilityDynamics\Veneer\Job' ) ) {

    /**
     * Class Job
     *
     * @class Job
     * @module Veneer
     */
    class Job extends \UsabilityDynamics\Job {

      /**
       * Initialize JOb
       *
       * @for Job
       */
      public function __construct( $settings ) {

        parent::__construct( $settings );

      }

    }

  }

}
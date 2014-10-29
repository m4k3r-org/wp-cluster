<?php
/**
 * Usability Dynamics RaaS Library
 *
 * @version 0.1.2
 * @module UsabilityDynamics
 */
namespace UsabilityDynamics\RaaS {

  if( !class_exists( 'UsabilityDynamics\RaaS\Job' ) ) {

    /**
     * RaaS Job
     *
     * @author team@UD
     * @extends UsabilityDynamics\Job
     * @version 0.1.2
     * @class RaaS
     * @module RaaS
     * @extends Utility
     */
    class Job extends \UsabilityDynamics\Job {

      /**
       * Job Instance Defaults.
       *
       * @property $defaults
       * @public
       * @type {Array}
       */
      public static $defaults = array(
        "type" => '_raas',
        "post_status" => 'job-ready',
        "post_type" => '_ud_job'
      );

      /**
       * Constructor for the Job class.
       *
       * @method __construct
       * @for Job
       * @constructor
       *
       * @param array|\UsabilityDynamics\object $settings array
       *
       * @return \UsabilityDynamics\RaaS\Job
       * @version 0.0.1
       * @since 0.0.1
       */
      public function __construct( $settings = array() ) {

        // Instantiate UsabilityDynamics\Job
        parent::__construct( $settings );

      }

    }
  }

}
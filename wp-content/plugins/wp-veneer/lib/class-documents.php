<?php
/**
 * UsabilityDynamics\Cluster Bootstrap
 *
 * @verison 0.4.1
 * @author potanin@UD
 * @namespace UsabilityDynamics\Cluster
 */
namespace UsabilityDynamics\Veneer {

  if( !class_exists( 'UsabilityDynamics\Veneer\Documents' ) ) {

    /**
     * Veneer Documents
     *
     * @class Bootstrap
     * @author potanin@UD
     * @version 0.0.1
     */
    class Documents {

      /**
       * Cluster core version.
       *
       * @static
       * @property $version
       * @type {Object}
       */
      public static $version = '0.4.1';

      /**
       * Constructor.
       *
       * UsabilityDynamics components should be avialable.
       * - class_exists( '\UsabilityDynamics\API' );
       * - class_exists( '\UsabilityDynamics\Utility' );
       *
       * @for Loader
       * @method __construct
       */
      public function __construct() {}

    }

  }

}

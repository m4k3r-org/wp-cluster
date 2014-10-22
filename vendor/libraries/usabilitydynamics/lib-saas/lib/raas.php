<?php
/**
 * Usability Dynamics RaaS Library
 *
 * @version 0.1.2
 * @module UsabilityDynamics
 */
namespace UsabilityDynamics {

  if( !class_exists( 'UsabilityDynamics\RaaS' ) ) {

    /**
     * RaaS Functions
     *
     * - UD_API_Key / ud::api_key
     * - UD_Site_UID / ud::site_uid
     * - UD_Public_Key / ud::public_key
     * - UD_Customer_Key / ud::customer_key
     *
     * @author team@UD
     * @version 0.1.2
     *
     * @class RaaS
     * @module RaaS
     * @extends Utility
     */
    class RaaS extends Utility {

      /**
       * RaaS Class version.
       *
       * @public
       * @static
       * @property $version
       * @type {Object}
       */
      public static $version = '0.1.2';

    }
  }

}
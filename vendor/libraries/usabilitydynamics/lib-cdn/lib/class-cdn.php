<?php
/**
 * CDN Class
 *
 * Should be extended by platform-specific CDN classes.
 *
 * @user potanin@UD
 *
 * @copyright Copyright (c) 2010 - 2013, Usability Dynamics, Inc.
 *
 * @author team@UD
 * @version 0.0.1
 * @namespace UsabilityDynamics
 * @module UsabilityDynamics
 */
namespace UsabilityDynamics {

  if( !class_exists( 'UsabilityDynamics\CDN' ) ) {

    /**
     * CDN Library.
     *
     * @submodule CDN
     * @version 0.0.1
     * @class Uti lity
     */
    class CDN {

      /**
       * Loader Class version.
       *
       * @property $version
       * @type {Object}
       */
      public static $version = '0.0.3';

      /**
       * Google Client Instance.
       *
       * @public
       * @static
       * @property $_storage
       * @type {Object}
       */
      public $_client = null;

      /**
       * Storage Service Instance.
       *
       * @public
       * @static
       * @property $_storage
       * @type {Object}
       */
      public $_storage = null;

      /**
       * Instantiate.
       *
       * @param {object|array} $settings Cofiguration.
       * @param {string} $settings.account
       * @param {string} $settings.key
       * @param {string} $settings.scopes
       */
      public function __construct( $settings ) {

      }

    }
  }

}

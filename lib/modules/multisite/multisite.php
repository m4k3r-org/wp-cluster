<?php
/**
 * MultiSite Capabilities
 *
 * Description: Control Premium Features on Multisite
 *
 * @author potanin@UD
 * @version 0.1.0
 *
 * @module Flawless
 * @submodule Multisite
 */
namespace Flawless {

  /**
   * Class Multisite
   *
   * @class Multisite
   * @module Flawless
   */
  class Multisite {

    /**
     * Something like constructor
     *
     */
    static function initialize() {

      //** Enable this Feature only if site is multisite */
      if ( !is_multisite() ) {
        return;
      }

      add_action( 'wpp_init', array( __CLASS__, 'init' ) );
      add_action( 'wpp_pre_init', array( __CLASS__, 'pre_init' ) );

    }

    /**
     * Special functions that must be called on init
     *
     */
    function init() {
    }

    /**
     * Special functions that must be called prior to init
     *
     */
    function pre_init() {
    }

    /**
     * Adds Menu pages to network
     */
    function network_pages() {
    }

  }

}
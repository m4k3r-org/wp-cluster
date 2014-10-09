<?php
/**
 * Plugin Name: Advanced Cache
 * Version: 0.1
 * Author: Usability Dynamics
 */

namespace UsabilityDynamics\Veneer {

  if( !class_exists( 'UsabilityDynamics\Veneer\AdvancedCache' ) ) {
    /**
     * This is our advanced cache handler
     * initialized and handled in the advanced-cache dropin
     */
    class AdvancedCache {

      /**
       * Holds our local cache
       */
      private $cache = array(
        'options'
      );

      /**
       * Adds our necessary actions
       */
      function add_actions() {

      }

      /**
       * Our constructor, hooks into all the proper filters
       *
       * @param bool $do_stuff Whether we should actually do initialization( needed for 'init' )
       */
      function __construct( $do_stuff = true ) {
        if( !( is_bool( $do_stuff ) && $do_stuff ) ) {
          return;
        }
        /** Go ahead and add our actions we need */
        $this->add_actions();
      }

      /**
       * This function lets us chain methods without having to instantiate first, YOU MUST COPY THIS TO ALL SUB CLASSES
       */
      static public function init() {
        return new self( false );
      }

    }
  }

  /**
   * If we don't have a wp_cluster object, we should make one, but it should be a child of wp_veneer
   */
  global $wp_veneer;
  if( !is_object( $wp_veneer ) ) {
    $wp_veneer = new \stdClass();
  }
  /** Now, add on our cache object, finally */
  if( !isset( $wp_veneer->advanced_cache ) ) {
    $wp_veneer->advanced_cache = AdvancedCache::init()->__construct();
  }

}
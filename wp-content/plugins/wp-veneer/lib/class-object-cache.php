<?php
/**
 * Plugin Name: Object Cache
 * Version: 0.1
 * Author: Usability Dynamics
 */

namespace UsabilityDynamics\Veneer {

  if( !class_exists( 'UsabilityDynamics\Veneer\ObjectCache' ) ){
    /**
     * This is our advanced cache handler
     * initialized and handled in the object-cache dropin
     */
    class ObjectCache{

      /**
       * Our constructor, hooks into all the proper filters
       * @param bool $do_stuff Whether we should actually do initialization( needed for 'init' )
       */
      function __construct( $do_stuff = true ){
        if( !( is_bool( $do_stuff ) && $do_stuff ) ){
          return;
        }
      }

      /**
       * This function lets us chain methods without having to instantiate first, YOU MUST COPY THIS TO ALL SUB CLASSES
       */
      static public function init(){
        return new self( false );
      }

    }
  }

  /**
   * If we don't have a wp_cluster object, we should make one, but it should be a child of wp_veneer
   */
  global $wp_veneer;
  if( !is_object( $wp_veneer ) ){
    $wp_veneer = new \stdClass();
  }
  /** Now, add on our cache object, finally */
  if( !isset( $wp_veneer->object_cache ) ){
    $wp_veneer->object_cache = ObjectCache::init()->__construct();
  }

}
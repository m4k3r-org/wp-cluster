<?php
/**
 * 
 *
 * @author Usability Dynamics
 * @namespace DiscoDonniePresents
 */
namespace DiscoDonniePresents\Eventbrite {

  if( !class_exists( 'DiscoDonniePresents\Eventbrite\Scaffold' ) ) {

    /**
     * Scaffold
     *
     * @author Usability Dynamics
     */
    class Scaffold {
      
      /**
       * Bootstrap Singleton object
       *
       * @var object DiscoDonniePresents\Eventbrite\Bootstrap
       */
      public $instance = NULL;
      
      /**
       * Constructor
       *
       * @author peshkov@UD
       */
      public function __construct() {
        //** Get our Bootstrap Singleton object */
        $this->instance =& get_wp_eventbrite();
      }
      
      /**
       * @param string $key
       * @param mixed $value
       *
       * @return \UsabilityDynamics\Settings
       */
      public function set( $key = null, $value = null ) {
        return $this->instance->set( $key, $value );
      }

      /**
       * @param string $key
       * @param mixed $default
       *
       * @return \UsabilityDynamics\type
       */
      public function get( $key = null, $default = null ) {
        return $this->instance->get( $key, $default );
      }

    }
  
  }

}

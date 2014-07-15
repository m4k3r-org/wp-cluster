<?php
/**
 * Evenbrite Core.
 * It's being loaded on 'after_setup_theme' action
 *
 * @author Usability Dynamics
 * @namespace DiscoDonniePresents
 */
namespace DiscoDonniePresents {

  if( !class_exists( 'DiscoDonniePresents\Eventbrite' ) ) {

    /**
     * Eventbrite Core
     *
     * @author Usability Dynamics
     */
    class Eventbrite {

      private $instance = NULL;
      
      /**
       * Constructor
       *
       * @author peshkov@UD
       */
      public function __construct() {
        //** Get our Bootstrap Singleton object */
        $this->instance =& get_wp_eventbrite();
        
        add_action( 'admin_init', array( $this, 'admin_init' ) );
      }
      
      /**
       *
       */
      public function admin_init() {
        //echo "<pre>"; print_r( $this->instance ); echo "</pre>"; die();
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

<?php
/**
 * Festival Bootstrap
 *
 * @version 0.1.0
 * @author Usability Dynamics
 * @namespace UsabilityDynamics
 */
namespace UsabilityDynamics\Festival {

  /**
   * Festival Bootstrap
   *
   * @author Usability Dynamics
   */
  final class Bootstrap {
  
    static private $instance;

    /**
     * Class Initializer
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    private function __construct() {
      
      if( !class_exists( '\UsabilityDynamics\Festival\Core' ) ) {
        wp_die( '<h1>Fatal Error</h1><p>Festival Theme not found.</p>' );
      }
      
      // Instantaite Disco.
      $this->theme = new Core;
      
    }
    
    /**
     * Return Theme Instance
     *
     */
    public function get_instance() {
      // Determine if instance already exists
      if ( null === self::$instance ) {
          // Inits new instance
          self::$instance = new self();
      }
      return self::$instance->theme;
    }
    

  }

}

<?php
/**
 * Menufication Wrapper
 * Adds additional functionality to default Menufication library
 *
 * @version 0.1.0
 * @author Usability Dynamics
 * @namespace UsabilityDynamics
 */
namespace UsabilityDynamics\Festival {

  /**
   * Menufication
   *
   * @author Usability Dynamics
   */
  class Menufication extends \Menufication {

    public function __construct() {
      parent::__construct();
      
      //@todo: finish
    }
    
    /**
     * Singleton
     */
    public static function getInstance() {
      if( !isset( self::$instance ) ) {
        self::$instance = new self;
      }
      return self::$instance;
    }

  }

}

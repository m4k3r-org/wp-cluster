<?php
/**
 * Evenbrite Client for Evenbrite API
 *
 * @author Usability Dynamics
 * @namespace DiscoDonniePresents
 */
namespace DiscoDonniePresents\Eventbrite {

  if( !class_exists( 'DiscoDonniePresents\Eventbrite\Client' ) ) {

    /**
     * Eventbrite Core
     *
     * @author Usability Dynamics
     */
    class Client extends \UsabilityDynamics\Eventbrite\Client {
      
      /**
       *
       */
      private static $connected = NULL;
    
      /**
       *
       */
      public $errors = array();
    
      /**
       * Determine if provided credentials are valid by doing test request
       */
      public function ping() {
        if( NULL === self::$connected ) {
          self::$connected = true;
          try{
            $user = $this->user_get()->user;
          }catch( \Exception $e ){
            self::$connected = false;
            $this->errors[] = $e->getMessage();
          }
        }
        return self::$connected;
      }
      
      /**
       *
       *
       */
      public function get_errors() {
        return trim( implode( ', ', $this->errors ) );
      }
    
    }
  
  }

}
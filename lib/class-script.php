<?php
/**
 * Global Custom Javascript
 * Uses theme customization API
 *
 * @author usabilitydynamics@UD
 * @see https://codex.wordpress.org/Theme_Customization_API
 * @version 0.1
 * @module UsabilityDynamics\AMD
 */
namespace UsabilityDynamics\AMD {

  if( !class_exists( '\UsabilityDynamics\AMD\Script' ) ) {

    class Script extends \UsabilityDynamics\AMD\Scaffold {
    
      /**
       * Constructor
       *
       */
      public function __construct( $args = array() ) {
      
        parent::__construct( $args );
        
      }
      
    }
    
  }

}


      
<?php
/**
 * Evenbrite Core.
 * It's being loaded on 'after_setup_theme' action
 *
 * @author Usability Dynamics
 * @namespace DiscoDonniePresents
 */
namespace DiscoDonniePresents\Eventbrite {

  if( !class_exists( 'DiscoDonniePresents\Eventbrite\Core' ) ) {

    /**
     * Eventbrite Core
     *
     * @author Usability Dynamics
     */
    class Core extends Scaffold {
      
      /**
       * UI Settings object
       *
       * @var object \UsabilityDynamics\UI\Settings
       */
      public $ui = NULL;
      
      /**
       * Constructor
       *
       * @author peshkov@UD
       */
      public function __construct() {
        parent::__construct();
        //** Setup Admin Interface */
        $this->ui = new \UsabilityDynamics\UI\Settings( $this->instance->settings, Utility::get_schema( 'schema.ui' ) );
      }

    }
  
  }

}

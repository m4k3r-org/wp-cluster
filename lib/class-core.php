<?php
/**
 * Evenbrite Core.
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
       * Constructor
       *
       * @author peshkov@UD
       */
      public function __construct() {
        parent::__construct();
        //** Register Custom Post Types, meta and set their taxonomies */
        \UsabilityDynamics\Model::define( Utility::get_schema( 'schema.structure' ) );
        
        //** Hooks */
        add_action( 'wp_loaded', array( $this, 'wp_loaded' ) );
      }
      
      /**
       *
       */
      public function wp_loaded() {
        //** Set our custom post types structure */
        $this->instance->structure = \UsabilityDynamics\Model::get();
        //echo "<pre>"; print_r( get_wp_eventbrite()->structure ); echo "</pre>"; die();
      }

    }
  
  }

}

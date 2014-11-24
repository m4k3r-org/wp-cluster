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
        add_action( 'wp_loaded', array( $this, 'wp_loaded' ), 0 );
      }
      
      /**
       * Update our settings after WP is loaded.
       *
       * @author peshkov@UD
       */
      public function wp_loaded() {
        //** Set our model data */
        $this->set( 'model.schema', $schema = \UsabilityDynamics\Model::getSchema() );
        $this->set( 'model.structure', $structure = \UsabilityDynamics\Model::get() );
        
        $types = array();
        foreach( (array)$schema[ 'types' ] as $type => $data ) {
          $types[ $data[ 'key' ] ] = $type;
        }
        $this->set( 'post_type', $types );
      }

    }
  
  }

}

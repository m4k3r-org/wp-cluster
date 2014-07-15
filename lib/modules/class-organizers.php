<?php
/**
 * Evenbrite Organizers.
 *
 * @author Usability Dynamics
 * @namespace DiscoDonniePresents
 */
namespace DiscoDonniePresents\Eventbrite {

  if( !class_exists( 'DiscoDonniePresents\Eventbrite\Organizers' ) ) {

    /**
     * Eventbrite Organizers
     *
     * @author Usability Dynamics
     */
    class Organizers extends Scaffold {

      /**
       * Constructor
       *
       * @author peshkov@UD
       */
      public function __construct() {
        parent::__construct();
        add_action( 'admin_menu', array( $this, 'admin_menu' ), 999 );
      }
      
      /**
       *
       */
      public function admin_menu() {
        global $submenu, $menu;
        
        add_submenu_page( 'eventbrite_settings', __( 'Eventbrite Organizers', $this->get( 'domain' ) ), __( 'Organizers', $this->get( 'domain' ) ), 'manage_options', 'eventbrite_organizers', array( $this, 'load_page' ) );
      }
      
      /**
       *
       */
      public function load_page() {
        
      }

    }
  
  }

}

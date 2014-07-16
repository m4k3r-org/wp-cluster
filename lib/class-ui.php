<?php
/**
 * UI
 *
 * @author Usability Dynamics
 * @namespace DiscoDonniePresents
 */
namespace DiscoDonniePresents\Eventbrite {

  if( !class_exists( 'DiscoDonniePresents\Eventbrite\UI' ) ) {

    /**
     *
     *
     * @author Usability Dynamics
     */
    class UI extends Scaffold {
      
      /**
       *
       */
      public $screens = array();

      /**
       * Constructor
       *
       * @author peshkov@UD
       */
      public function __construct() {
        parent::__construct();
        
        //** Setup Admin Interface */
        new \UsabilityDynamics\UI\Settings( $this->instance->settings, Utility::get_schema( 'schema.ui' ) );
        
        add_action( 'admin_menu', array( $this, 'admin_menu' ), 999 );
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
      }
      
      /**
       *
       */
      public function admin_menu() {
        global $submenu, $menu;
        
        $this->screens[ 'organizers' ] = add_submenu_page( 'eventbrite_settings', __( 'Eventbrite Organizers', $this->get( 'domain' ) ), __( 'Organizers', $this->get( 'domain' ) ), 'manage_options', 'eventbrite_organizers', array( $this, 'load_page' ) );
        
      }
      
      /**
       *
       */
      public function load_page() {
        global $current_screen;
        $data = array();
        
        switch( true ) {
          
          case ( $this->screens[ 'organizers' ] == $current_screen->id ):
            $data = array(
              'organizers' => '',
            );
            $this->get_template_part( 'organizers/main', $data );
            break;
          
        }

      }
      
      /**
       * Handles notice messages on Organizers page
       * 
       * @author peshkov@UD
       */
      public function admin_notices() {
        global $current_screen;
        
        if( !in_array( $current_screen->id, $this->screens ) ) {
          return null;
        }
        
        $message = "";
        $error = false;
        switch( true ) {
        
          case ( $this->client === NULL ):
            $error = true;
            $message = __( 'Please, check your Eventbrite API credentials on Settings page before proceed.', $this->get( 'domain' ) );
            break;
        
          case ( !$this->client->ping() ):
            $error = true;
            $message = sprintf( __( 'Connection to Eventbrite API is aborted. %s', $this->get( 'domain' ) ), $this->client->get_errors() );
            break;
            
          case ( isset( $_REQUEST[ 'message' ] ) && $_REQUEST[ 'message' ] == 'updated' ):
            $message = __( 'Organizers updated', $this->get( 'domain' ) );
            break;
        
        }
        
        if( !empty( $message ) ) {
          echo '<div class="' . ( $error ? 'error' : 'updated' ) . ' fade">' . $message . '</div>';
        }
      }
      
    }
  
  }

}

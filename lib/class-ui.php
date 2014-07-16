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
       *
       */
      public $errors = array();
      
      /**
       *
       */
      public $messages = array();

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
        add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
      }
      
      /**
       *
       */
      public function admin_menu() {
        global $submenu, $menu;
        
        $this->screens[ 'organizers' ] = add_submenu_page( 'eventbrite_settings', __( 'Eventbrite Organizers', $this->get( 'domain' ) ), __( 'Organizers', $this->get( 'domain' ) ), 'manage_options', 'eventbrite_organizers', array( $this, 'load_page' ) );
        
        $this->screens = apply_filters( 'eventbrite::screens', $this->screens );
        
        //** Add requests interceptors */
        foreach( $this->screens as $id ) {
          add_action( 'load-' . $id, array( $this, 'request' ) );
        }
        
      }
      
      /**  
       * Handles requests
       *
       */
      public function request() {
        global $current_screen;
        
        //** Be sure that user has capabilities */
        if( !current_user_can( 'manage_options' ) ) {
          return null;
        }
        
        switch( true ) {
          
          //** Synchronize Organizers with Eventbrite */
          case ( $this->screens[ 'organizers' ] == $current_screen->id && isset( $_REQUEST[ 'sync' ] ) && $_REQUEST[ 'sync' ] == true ):
            $r = Organizers::sync();
            if( is_wp_error( $r ) ) {
              array_push( $this->errors, $r->get_error_message() );
            } else {
              array_push( $this->messages, __( 'Organizers have been successfully synchronized.', $this->get( 'domain' ) ) );
            }
            break;
        
        }
      
      }
      
      /**
       * Loads Scripts and Styles
       *
       */
      public function load_assets() {
        global $current_screen;
        
        if( !in_array( $current_screen->id, $this->screens ) ) {
          return null;
        }
        
        //** Enqueue global scripts and styles */
        wp_enqueue_style( 'eventbrite-admin-global', WP_EVENTBRITE_URL . 'static/styles/admin.global.css' );
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
              'organizers' => Organizers::get_organizers(),
            );
            $this->get_template_part( 'admin.organizers', $data );
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

          case ( !empty( $this->errors ) ):
            $error = true;
            $message = trim( implode( '<br/>', $this->errors ) );
            break;
            
          case ( !empty( $this->messages ) ):
            $message = trim( implode( '<br/>', $this->messages ) );
            break;
        
          case ( !$this->instance->client->ping() ):
            $error = true;
            $message = sprintf( __( 'Connection to Eventbrite API is aborted. Please, check your Eventbrite API credentials on <a href="%s">Settings</a> page. Response: %s', $this->get( 'domain' ) ), admin_url( 'admin.php?page=eventbrite_settings' ), $this->instance->client->get_errors() );
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

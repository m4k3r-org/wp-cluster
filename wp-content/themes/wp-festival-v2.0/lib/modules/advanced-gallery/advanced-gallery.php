<?php

/**
 * Carrington Build module Advanced Gallery
 * 
 * 16 images 45 deg rotated gallery
 */
if( !class_exists( 'UsabilityDynamics_Festival2_AdvancedGalleryModule' ) ) {

  /**
   * UsabilityDynamics_Festival2_AdvancedGalleryModule
   */
  class UsabilityDynamics_Festival2_AdvancedGalleryModule extends \UsabilityDynamics\Theme\Module {
    
    /**
     * Construct
     */
    public function __construct() {
      
      wp_enqueue_media();
      wp_enqueue_script('knockout', 'http://cdnjs.cloudflare.com/ajax/libs/knockout/3.1.0/knockout-min.js');

      $opts = array(
        'description' => __( 'Display Advanced Gallery for home page.', wp_festival2( 'domain' ) ),
        'icon' => plugins_url( basename( __DIR__ ) . '/icon.png', __DIR__ . '/' )
      );
      parent::__construct( 'cfct-module-advanced-gallery', __( 'Advanced Gallery', wp_festival2( 'domain' ) ), $opts );

    }
    
    /**
     * Module CSS
     * @return type
     */
    public function admin_css() {
      ob_start();
      
      ?>
        #gallery-media-images-list {
          width: 100%;
          height: 200px;
          overflow-x: scroll;
        }
        #gallery-media-images-list li {
          background: white;
          float: left;
          padding: 5px;
          margin: 5px;
          height: 75px;
        }
        #gallery-media-images-list li img {
          max-width: 50px;
          max-height: 50px;
        }
      <?php
      
      return ob_get_clean();
    }

    /**
     * Display the module
     *
     * @param array $data - saved module data
     * @param array $args - previously set up arguments from a child class
     *
     * @return string HTML
     */
    public function display( $data ) {
      return $this->load_view( $data );
    }
    
    /**
     * Admin form
     * 
     * @param type $data
     * @return type
     */
    public function admin_form( $data ){
      ob_start();
      require_once( __DIR__ . '/admin.php' );
      return ob_get_clean();
    }
    
    /**
     * 
     * @return null
     */
    public function text() {
      return null;
    }

  }
  
}
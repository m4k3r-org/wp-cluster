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

    /**
     * Constructor
     */
    public function __construct() {
      parent::__construct();
      
      add_action( 'wp_footer', array( $this, 'render_html' ), 100 );
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
    
    /**
     * Replace original method with dummy one.
     * Note: we don't need to print menufication scripts because they
     * already reigistered and called by requirejs
     * So just get rid of extra javascript files
     * 
     * @see scripts/src/app.config.js
     */
    public function add_js() {}
    
    /**
     * Renders all our additional elements
     * to the hidden block in footer.
     * They are handled by javascript and 
     * being moved to 'menufication' menu on initialization.
     *
     * @see scripts/src/menufication.advanced.js
     */
    public function render_html() {
      echo "<div style=\"display:none !important;\">";
      get_template_part( 'templates/nav/menufication' );
      echo "</div>";
    }

  }

}

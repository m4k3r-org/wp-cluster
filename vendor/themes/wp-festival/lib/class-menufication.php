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
      
      add_filter( 'udx:theme:script:config', array( $this, 'add_configuration' ) );
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
     * Configuration parameters for menufication initialization
     * used in scripts/app.main.js
     * 
     * @filter udx:theme:script:config
     * @see \UsabilityDynamics\Theme\Scaffold::_print_scripts()
     */
    public function add_configuration( $config ) {
      global $post;
      
      /** Determine if we should disable menufication for the current page */
      $triggerWidth = "770";
      $disabled_menus = get_post_meta( $post->ID, 'disabledNavMenu' );
      if( !empty( $disabled_menus ) && in_array( 'menufication', $disabled_menus ) ) {
        $triggerWidth = "1";
      }
      
      $config[ 'menufication' ] = array( 
        "element"             => "#wp_menufication",
        "menuLogo"            => "",
        "menuText"            => "",
        "triggerWidth"        => $triggerWidth,
        "addHomeLink"         => null, 
        "addHomeText"         => "",
        "addSearchField"      => null, 
        "hideDefaultMenu"     => "on",
        "onlyMobile"          => null, 
        "direction"           => "left",
        "theme"               => "dark",
        "disableCSS"          => "on",
        "childMenuSupport"    => "on",
        "childMenuSelector"   => "sub-menu, children",
        "activeClassSelector" => "current-menu-item, current-page-item, active",
        "enableSwipe"         => "on",
        "doCapitalization"    => null, 
        "supportAndroidAbove" => "3.5",
        "disableSlideScaling" => null, 
        "toggleElement"       => "",
        "customMenuElement"   => "",
        "customFixedHeader"   => "",
        "addToFixedHolder"    => "",
        "page_menu_support"   => null, 
        "wrapTagsInList"      => "",
        "allowedTags"         => "DIV, NAV, UL, OL, LI, A, P, H1, H2, H3, H4, SPAN, FORM, INPUT, SEARCH",
        "customCSS"           => "",
        "is_page_menu"        => "",
        "enableMultiple"      => "",
        "is_user_logged_in"   => ""
      );
      
      return $config;
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

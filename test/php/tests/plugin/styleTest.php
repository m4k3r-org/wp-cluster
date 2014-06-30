<?php
/**
 * 
 * @class StyleTest
 */
class StyleTest extends Post_AMD_WP_UnitTestCase {

  /**
   * Set fixture
   */
  function setAMDPostObject() {
    if( !is_object( $this->instance->style ) || get_class( $this->instance->style ) != 'UsabilityDynamics\AMD\Style' ) {
      $this->fail( 'Style object is not available.' );
    }
    $this->asset = $this->instance->style;
  }
  
  /**
   *
   */
  function checkRegisteredAsset() {
    global $wp_styles;
    
    $dependencies = (array)$this->asset->get( 'dependencies' );    
    $style = !empty( $wp_styles[ 'wp-amd-script' ] ) ? $wp_styles[ 'wp-amd-script' ] : false;
    
    $this->assertTrue( !empty( $style ) );
    
    // Do nothing if asset does not exist or not use dependencies.
    if( !empty( $style ) && !empty( $dependencies ) ) {
      
    }
  }
  
  /**
   *
   * @group asset
   * @group style
   */
  function testSpecificActions() {
    $this->assertGreaterThan( 0, has_filter( 'wp_print_styles', array( $this->asset, 'register_asset' ) ) );
  }
  
}

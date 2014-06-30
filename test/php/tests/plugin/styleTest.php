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
   * @group asset
   * @group style
   */
  function testSpecificActions() {
    $this->assertGreaterThan( 0, has_filter( 'wp_print_styles', array( $this->asset, 'register_asset' ) ) );
  }
  
}

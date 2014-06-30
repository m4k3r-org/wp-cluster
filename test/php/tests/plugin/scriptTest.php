<?php
/**
 * 
 * @class ScriptTest
 */
class ScriptTest extends Post_AMD_WP_UnitTestCase {

  /**
   * Set fixture
   */
  function setAMDPostObject() {
    if( !is_object( $this->instance->script ) || get_class( $this->instance->script ) != 'UsabilityDynamics\AMD\Script' ) {
      $this->fail( 'Script object is not available.' );
    }
    $this->asset = $this->instance->script;
  }
  
  /**
   *
   * @group post
   * @group asset
   */
  function testSpecificActions() {
    $this->assertGreaterThan( 0, has_filter( 'wp_enqueue_scripts', array( $this->asset, 'register_asset' ) ) );
  }
  
}

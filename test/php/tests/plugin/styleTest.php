<?php
/**
 * 
 * @class StyleTest
 */
class StyleTest extends Post_AMD_WP_UnitTestCase {

  /**
   *
   */
  function setAMDPostObject() {
    if( !is_object( $this->instance->style ) || get_class( $this->instance->style ) != 'UsabilityDynamics\AMD\Style' ) {
      $this->fail( 'Style object is not available.' );
    }
    $this->pobj = $this->instance->style;
  }
  
}

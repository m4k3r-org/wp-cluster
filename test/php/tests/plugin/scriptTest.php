<?php
/**
 * 
 * @class ScriptTest
 */
class ScriptTest extends Post_AMD_WP_UnitTestCase {

  /**
   *
   */
  function setAMDPostObject() {
    if( !is_object( $this->instance->script ) || get_class( $this->instance->script ) != 'UsabilityDynamics\AMD\Script' ) {
      $this->fail( 'Script object is not available.' );
    }
    $this->pobj = $this->instance->script;
  }
  
}

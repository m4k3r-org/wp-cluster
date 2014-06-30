<?php
/**
 * Just make sure the test framework is working
 *
 * @class BasicTest
 */
class CoreTest extends AMD_WP_UnitTestCase {

  /**
   * 
   * @group core
   */
  function test_get_wp_amd() {
    $this->assertTrue( function_exists( 'get_wp_amd' ) );
  }
  
  /**
   *
   * @group core
   */
  function test_instance() {
    $this->assertTrue( is_object( $this->instance ) );
  }
  
}

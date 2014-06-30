<?php
/**
 * 
 * @class Post_AMD_WP_UnitTestCase
 */
class Post_AMD_WP_UnitTestCase extends AMD_WP_UnitTestCase {

  protected $pobj;
  
  /**
   *
   */
  function setUp() {
	  parent::setUp();
    $this->setAMDPostObject();
  }
  
  /**
   *
   */
  function tearDown() {
	  parent::tearDown();
    $this->pobj = NULL;
  }
  
  /**
   *
   */
  function setAMDPostObject() {
    $this->fail( 'setAMDPostObject must be rewritten' );
  }
  
  /**
   *
   * @group post
   */
  function testPobj() {
    $this->assertTrue( is_object( $this->pobj ) );
    
  }
  
  /**
   * Test Registering of Post
   *
   * @group post
   */
  function testRegisterPostType() {
    $this->assertNull( get_post_type_object( $this->pobj->get( 'post_type' ) ) );
    $this->pobj->register_post_type();
    
    $pobj = get_post_type_object( $this->pobj->get( 'post_type' ) );
    $this->assertInstanceOf( 'stdClass', $pobj );
    $this->assertEquals( $this->pobj->get( 'post_type' ), $pobj->name );

    // Test if post supports revisions
    $this->assertTrue( post_type_supports( $this->pobj->get( 'post_type' ), 'revisions' ) );

    _unregister_post_type( $this->pobj->get( 'post_type' ) );
  }
  
  /**
   * Test Actions
   *
   * @group post
   */
  function testActions() {
    $this->assertGreaterThan( 0, has_filter( 'init', array( $this->pobj, 'register_post_type' ) ) );
    $this->assertGreaterThan( 0, has_filter( 'admin_init', array( $this->pobj, 'admin_init' ) ) );
    $this->assertGreaterThan( 0, has_filter( 'redirect_canonical', array( $this->pobj, 'redirect_canonical' ) ) );
    $this->assertGreaterThan( 0, has_filter( 'query_vars', array( 'UsabilityDynamics\AMD\Scaffold', 'query_vars' ) ) );
    $this->assertGreaterThan( 0, has_filter( 'pre_update_option_rewrite_rules', array( $this->pobj, 'update_option_rewrite_rules' ) ) );
    $this->assertGreaterThan( 0, has_filter( 'template_include', array( 'UsabilityDynamics\AMD\Scaffold', 'return_asset' ) ) );
  }
  
}

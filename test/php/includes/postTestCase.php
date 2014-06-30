<?php
/**
 * 
 * @class Post_AMD_WP_UnitTestCase
 */
class Post_AMD_WP_UnitTestCase extends AMD_WP_UnitTestCase {

  /**
   * @type object
   */
  protected $asset;
  
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
    $this->asset = NULL;
  }
  
  /**
   * Rewrite the method in child class!
   */
  function setAMDPostObject() {
    $this->fail( 'setAMDPostObject must be rewritten' );
  }
  
  /**
   *
   * @group asset
   */
  function testPobj() {
    $this->assertTrue( is_object( $this->asset ) );
    // Test get settings
    $this->assertNotEmpty( $this->asset->get( 'post_type' ) );
  }
  
  /**
   * Test Registering of Post
   *
   * @group asset
   */
  function testRegisterPostType() {
    $this->assertNull( get_post_type_object( $this->asset->get( 'post_type' ) ) );
    $this->asset->register_post_type();
    
    $pobj = get_post_type_object( $this->asset->get( 'post_type' ) );
    $this->assertInstanceOf( 'stdClass', $pobj );
    $this->assertEquals( $this->asset->get( 'post_type' ), $pobj->name );

    // Test if post supports revisions
    $this->assertTrue( post_type_supports( $this->asset->get( 'post_type' ), 'revisions' ) );

    _unregister_post_type( $this->asset->get( 'post_type' ) );
  }
  
  /**
   * Test Actions
   *
   * @group asset
   */
  function testGeneralActions() {
    $this->assertGreaterThan( 0, has_filter( 'init', array( $this->asset, 'register_post_type' ) ) );
    $this->assertGreaterThan( 0, has_filter( 'admin_init', array( $this->asset, 'admin_init' ) ) );
    $this->assertGreaterThan( 0, has_filter( 'redirect_canonical', array( $this->asset, 'redirect_canonical' ) ) );
    $this->assertGreaterThan( 0, has_filter( 'pre_update_option_rewrite_rules', array( $this->asset, 'update_option_rewrite_rules' ) ) );
    // @todo: It's being tested twice. Need to moved it out.
    $this->assertGreaterThan( 0, has_filter( 'query_vars', array( 'UsabilityDynamics\AMD\Scaffold', 'query_vars' ) ) );
    $this->assertGreaterThan( 0, has_filter( 'template_include', array( 'UsabilityDynamics\AMD\Scaffold', 'return_asset' ) ) );
    
    //** Determine if Admin Menu is enabled */
    if( $this->asset->get( 'admin_menu' ) ) {
      $this->assertGreaterThan( 0, has_filter( 'admin_menu', array( $this->asset, 'add_admin_menu' ) ) );
      // @todo: It's being tested twice. Need to moved it out.
      $this->assertGreaterThan( 0, has_filter( 'get_edit_post_link', array( 'UsabilityDynamics\AMD\Scaffold', 'revision_post_link' ) ) );
    }
  }
  
  /**
   *
   * @group asset
   */
  function testAsset() {
    $path = $this->root_dir . '/test/php/fixtures/sample.' . $this->asset->get( 'extension' );
    if( file_exists( $path ) ) {
      $content = @file_get_contents( $path );
    }
    if( empty( $content ) ) {
      $this->fail( 'Fixture data for testing is not available.' );
    }
    
    //** Save Asset */
    $this->assertGreaterThan( 0, $this->asset->save_asset( $content ) );
    
    //** Get Asset */
    $post = $this->asset->get_asset( $this->asset->get( 'type' ) );
    $this->assertTrue( is_array( $post ) );
    $this->assertArrayHasKey( 'ID', (array)$post );
  }
  
}

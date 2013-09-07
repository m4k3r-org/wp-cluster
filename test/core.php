<?php
require_once '../functions.php';
require_once 'PHPUnit.php';

class CoreTest extends PHPUnit_TestCase {

  var $abc;

  function CoreTest( $name ) {
    $this->PHPUnit_TestCase( $name );
  }

  function setUp() {
    $this->abc = new String( "abc" );
  }

  function tearDown() {
    unset( $this->abc );
  }

  function testToString() {

    if( class_exists( 'TCPDF' ) ) { echo "TCPDF exists.\n"; }
    if( class_exists( 'Datamatrix' ) ) { echo "Datamatrix exists.\n"; }
    if( class_exists( 'UsabilityDynamics\API' ) ) { echo "UsabilityDynamics\API exists.\n"; }
    if( class_exists( 'UsabilityDynamics\Loader' ) ) { echo "UsabilityDynamics\Loader exists.\n"; }
    if( class_exists( 'UsabilityDynamics\Settings' ) ) { echo "UsabilityDynamics\Settings exists.\n"; }
    if( class_exists( 'Monolog\Logger' ) ) { echo "Monolog\Logger exists.\n"; }
    if( class_exists( 'Flawless\API' ) ) { echo "Flawless\API exists.\n"; }
    if( class_exists( 'Flawless\Legacy' ) ) { echo "Flawless\Legacy exists.\n"; }
    if( class_exists( 'Flawless\Settings' ) ) { echo "Flawless\Settings exists.\n"; }

    echo 'API::$version ' . API::$version . ".\n";
    echo '\Flawless\API::$version ' . \Flawless\API::$version . ".\n";
    echo '\UsabilityDynamics\API::$version ' . \UsabilityDynamics\API::$version . ".\n";

  }

  function testCopy() {
    $abc2 = $this->abc->copy();
    $this->assertEquals( $abc2, $this->abc );
  }

  function testAdd() {
    $abc2 = new String( '123' );
    $this->abc->add( $abc2 );
    $result = $this->abc->toString( "%s" );
    $expected = "abc123";
    $this->assertTrue( $result == $expected );
  }
}

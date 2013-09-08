<?php
require_once '../functions.php';
require_once 'PHPUnit.php';

class CoreTest extends PHPUnit_TestCase {

  function CoreTest( $name ) {
    $this->PHPUnit_TestCase( $name );
  }

  function classesExist() {

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

  function validationWorks() {

    $test = new Schema( '../static/schemas/test-data.json', '../static/schemas/schema-test.json' );
    die( '<pre>' . print_r( $test, true ) . '</pre>' );

  }

  function pathsWork() {

    /*

    require_once( $this->paths->controllers . '/api.php' );
    require_once( $this->paths->controllers . '/asset.php' );
    require_once( $this->paths->controllers . '/content.php' );
    require_once( $this->paths->controllers . '/element.php' );
    require_once( $this->paths->controllers . '/legacy.php' );
    require_once( $this->paths->controllers . '/license.php' );
    require_once( $this->paths->controllers . '/log.php' );
    require_once( $this->paths->controllers . '/module.php' );
    require_once( $this->paths->controllers . '/shortcode.php' );
    require_once( $this->paths->controllers . '/theme.php' );
    require_once( $this->paths->controllers . '/utility.php' );
    require_once( $this->paths->controllers . '/views.php' );
    require_once( $this->paths->controllers . '/widget.php' );

    */

  }



}

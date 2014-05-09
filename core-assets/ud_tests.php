<?php

/**
 * UD Tests
 *
 * Description: Test various things.
 *
 * @author team@UD
 * @version 0.1
 * @package UD
 * @subpackage Functions
 */
class UD_Tests {

  static function http_methods( $protocol = 'http://' ) {

    //$response = wp_remote_request( $protocol . 'cloud.usabilitydynamics.com/tests.json/client', array( 'method' => '' ));

    echo '<pre>' . print_r( wp_remote_request( $protocol . 'cloud.usabilitydynamics.com/tests.json/client', array( 'method' => 'GET' ) ), true ) . '</pre>';
    echo '<pre>' . print_r( wp_remote_request( $protocol . 'cloud.usabilitydynamics.com/tests.json/client', array( 'method' => 'NOTIFY' ) ), true ) . '</pre>';
    echo '<pre>' . print_r( wp_remote_request( $protocol . 'cloud.usabilitydynamics.com/tests.json/client', array( 'method' => 'MOVE' ) ), true ) . '</pre>';
    echo '<pre>' . print_r( wp_remote_request( $protocol . 'cloud.usabilitydynamics.com/tests.json/client', array( 'method' => 'MERGE' ) ), true ) . '</pre>';
    echo '<pre>' . print_r( wp_remote_request( $protocol . 'cloud.usabilitydynamics.com/tests.json/client', array( 'method' => 'LOCK' ) ), true ) . '</pre>';
    echo '<pre>' . print_r( wp_remote_request( $protocol . 'cloud.usabilitydynamics.com/tests.json/client', array( 'method' => 'TRACE' ) ), true ) . '</pre>';
    echo '<pre>' . print_r( wp_remote_request( $protocol . 'cloud.usabilitydynamics.com/tests.json/client', array( 'method' => 'UNLOCK' ) ), true ) . '</pre>';
    echo '<pre>' . print_r( wp_remote_request( $protocol . 'cloud.usabilitydynamics.com/tests.json/client', array( 'method' => 'REPORT' ) ), true ) . '</pre>';
    echo '<pre>' . print_r( wp_remote_request( $protocol . 'cloud.usabilitydynamics.com/tests.json/client', array( 'method' => 'SUBSCRIBE' ) ), true ) . '</pre>';
    echo '<pre>' . print_r( wp_remote_request( $protocol . 'cloud.usabilitydynamics.com/tests.json/client', array( 'method' => 'UNSUBSCRIBE' ) ), true ) . '</pre>';

    die( '- SUCCESS -' );
    //die( '<pre>' . print_r( wp_remote_request( $protocol . 'cloud.usabilitydynamics.com/tests.json/client', array( 'method' => 'NOTIFY' )) true )  . '</pre>' );

  }

}

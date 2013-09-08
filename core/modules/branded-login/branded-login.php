<?php
/**
 * Name: Branded Login
 * Description: The UI for the Flawless theme.
 * Author: Usability Dynamics, Inc.
 * Version: 1.0
 * Copyright 2010 - 2012 Usability Dynamics, Inc.
 *
 * @name Branded Login
 * @description The UI for the Flawless theme.
 * @author Usability Dynamics, Inc.
 * @version 1.0
 * @module Branded Login
 * @namespace Flawless
 */
namespace Flawless {

  /**
   * Class Branded_Login
   *
   * @class Branded_Login
   * @module Flawless
   */
  class Branded_Login {

    /**
     *
     * @for Branded_Login
     */
    function __construct() {

      add_action( 'flawless::init_upper', array( __CLASS__, 'init_upper' ) );

      //** Change login page logo URL */
      add_action( 'login_headerurl', create_function( '', ' return home_url(); ' ) );
      add_action( 'login_headertitle', create_function( '', ' return get_bloginfo( "name" ); ' ) );

    }

    /**
     *
     */
    function init_upper() {

      //** Add custom logo to login screen */
      add_action( 'login_head', array( __CLASS__, 'login_head' ) );

    }

    /**
     * Adds custom logo, if exists, to login screen.
     *
     * @since 0.0.2
     */
    static function login_head() {
      global $flawless;

      if ( !Asset::can_get_image( $flawless[ 'flawless_logo' ][ 'url' ] ) ) {
        return;
      }

      echo '<style type="text/css" media="screen">.login h1 a, #login { min-width: 300px; width: ' . $flawless[ 'flawless_logo' ][ 'width' ] . 'px; } .login h1 a { background-size:' . $flawless[ 'flawless_logo' ][ 'width' ] . 'px ' . $flawless[ 'flawless_logo' ][ 'height' ] . 'px; background-image: url( ' . $flawless[ 'flawless_logo' ][ 'url' ] . ' ); margin-bottom: 10px;} </style>';

    }

    /**
     * Modifies default WP Login form by adding extra classes
     *
     */
    static function wp_login_form( $args = false ) {

      //* Must override */
      $args[ 'echo' ] = false;

      $form = wp_login_form( $args );

      //** Add our classes */
      $form = str_replace( 'name="log"', 'name="log" placeholder="Username"', $form );
      $form = str_replace( 'name="pwd"', 'name="pwd" placeholder="Password"', $form );

      echo $form;

    }

  }

}

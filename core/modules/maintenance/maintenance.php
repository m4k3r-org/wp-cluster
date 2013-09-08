<?php
/**
 * Name: Maintenence
 * Version: 1.0
 * Description: Widgets for the Flawless theme.
 * Author: Usability Dynamics, Inc.
 * Theme Feature: maintenence-mode
 *
 * @name Maintenence
 * @description Widgets for the Flawless theme.
 * @author Usability Dynamics, Inc.
 * @version 1.0
 * @namespace Flawless
 * @module Maintenence
 */
namespace Flawless {

  /**
   * Class Maintenance
   *
   * @class Maintenance
   * @extends Module
   */
  class Maintenance extends Module {

    function __construct() {

      add_action( 'flawless::template_redirect', array( __CLASS__, 'template_redirect' ), 10 );

    }

    /**
     *
     *
     * @method template_redirect
     */
    function template_redirect() {
      global $wp_query, $flawless;

      if ( $flawless[ 'maintanance_mode' ] == 'true' ) {

        $wp_query->query_vars[ 'splash_screen' ] = true;

        if ( file_exists( untrailingslashit( get_stylesheet_directory() ) . '/maintanance.php' ) ) {
          include untrailingslashit( get_stylesheet_directory() ) . '/maintanance.php';
          die();
        } else {
          include TEMPLATEPATH . '/maintanance.php';
          die();
        }
      }

    }

  }

}
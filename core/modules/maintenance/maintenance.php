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

    /**
     * Constructor for the Maintenance class.
     *
     * @author potanin@UD
     * @version 0.0.1
     * @method __construct
     *
     * @constructor
     * @for Maintenance
     *
     * @param array $options
     */
    public function __construct( $options = array() ) {
      add_action( 'flawless::wp_print_styles', array( __CLASS__, 'wp_print_styles' ), 10 );
      add_action( 'flawless::template_redirect', array( __CLASS__, 'template_redirect' ), 10 );
    }

    /**
     * Frontend Loader
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

    /**
     * Enqueue Maintanance CSS only when in Maintanance Mode
     *
     * @param $flawless
     */
    function wp_print_styles( $flawless ) {
      global $wp_query;

      if ( $wp_query->query_vars[ 'splash_screen' ] && Asset::load( 'flawless-maintanance.css', 'css' ) ) {
        wp_enqueue_style( 'flawless-maintanance', Asset::load( 'flawless-maintanance.css', 'css' ), array( 'flawless-style' ), Flawless_Version );
      }

    }

  }

}
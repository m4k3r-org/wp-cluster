<?php
/**
 * CSS Customizer
 *
 * @author potanin@UD
 * @author peshkov@UD
 * @author korotkov@UD
 *
 * @version 0.1
 * @module UsabilityDynamics
 */
namespace UsabilityDynamics\AMD {

  if( !class_exists( 'UsabilityDynamics\AMD\Style' ) ) {

    class Style {
    
      /**
       *
       * UsabilityDynamics\AMD\Style::enable_style_customizer( array( 'name'  => 'app-style' ));
       *
       * Built-in Sections:
       * - title_tagline
       * - colors
       * - header_image
       * - background_image
       * - nav
       * - static_front_page
       *
       * add_section options
       * - capability
       * - priority
       * - title
       * - description
       *
       * add_setting options
       * - default
       * - type
       * - transport
       *
       * @todo Implement better way of registering library scripts (ace.js, script-editor.js, etc.).
       * @todo How to pass $args into callback function?
       *
       * @param array $_atts
       * @return bool
       * @internal param $args
       * @author potanin@UD
       */
      static function define( $_atts = array()  ) {

        $args = (object) shortcode_atts( array(
          'name'  => 'app.style',
          'deps'  => array(),
          'version' => '1.0'
        ), $_atts );

        // Enqueue Frontend Style.
        add_action( 'wp_enqueue_scripts', function() {
          wp_register_style( 'app.style', home_url() . '/app.style.css', array(), 1.0 );
        });

        // Enable JavaScript Library Loading.
        /* new \UsabilityDynamics\Requires( array(
          'name' => 'ui.editor',
          'scope' => [ 'backend' ],
          'debug' => true
        ));
        // Enable JavaScript Library Loading.
        new \UsabilityDynamics\Requires( array(
          'name' => 'ui.customizer',
          'scope' => [ 'customizer' ],
          'debug' => true
        )); */

        if( !did_action( 'customize_register' ) ) {
          add_action( 'customize_register', array( __CLASS__, 'register_style_customizer' ) );
        }

        // Handle Requests.
        add_action( 'template_redirect', array( __CLASS__, 'serve_custom_assets' ) );

      }
      
      /**
       * Register Sections, Settings, Controls, etc.
       *
       * @author potanin@UD
       * @param $wp_customize
       */
      static function register_style_customizer( $wp_customize ) {

        // Configure Section.
        $wp_customize->add_section( 'style-customizer', array(
          'title'    => __( 'Theme Styles' ),
          'description' => __( 'Handle custom CSS styles that will be loaded after all other styles.' ),
          'capability' => 'edit_theme_options',
          'priority' => -20
        ));

        // Stores raw CSS.
        $wp_customize->add_setting( 'custom-style', array(
          'type'       => 'theme_mod',
          'capability' => 'edit_theme_options',
          'transport' => 'postMessage'
        ));

        // Minification Option.
        $wp_customize->add_setting( 'custom-style-minify', array(
          'default'       => false,
          'type'          => 'theme_mod',
          'capability'    => 'edit_theme_options',
          'transport'     => 'postMessage'
        ));

        // Caching Option.
        $wp_customize->add_setting( 'custom-style-cache', array(
          'default'       => true,
          'type'          => 'theme_mod',
          'capability'    => 'edit_theme_options',
          'transport'     => 'postMessage'
        ));

        // Input for CSS Code.
        $wp_customize->add_control( new Style_Editor_Control( $wp_customize, 'custom-style', array(
          'label'   => __( 'CSS' ),
          'section' => 'style-customizer',
          'priority' => 10
        )));

        // Basic Checkbox.
        $wp_customize->add_control( 'custom-style-minify', array(
          'label'   => __( 'Minify Output' ),
          'settings' => 'custom-style-minify',
          'section' => 'style-customizer',
          'type'    => 'checkbox',
          'priority' => 20
        ));

        // Basic Checkbox.
        $wp_customize->add_control( 'custom-style-cache', array(
          'label'   => __( 'Allow Caching' ),
          'settings' => 'custom-style-cache',
          'section' => 'style-customizer',
          'type'    => 'checkbox',
          'priority' => 30
        ));

        // Make Setting Magical.
        $wp_customize->get_setting( 'custom-style' )->transport = 'postMessage';
        $wp_customize->get_setting( 'custom-style-minify' )->transport = 'postMessage';
        $wp_customize->get_setting( 'custom-style-cache' )->transport = 'postMessage';

      }
      
      /**
       * Servce Custom CSS File
       *
       * @todo I know this is ghetto.
       * @author potanin@UD
       */
      static function serve_custom_assets() {

        // Somenbody beat us to it.
        if( headers_sent() ) {
          return;
        }

        // Serve CSS.
        if( isset( $_SERVER[ 'REDIRECT_URL' ] ) && $_SERVER[ 'REDIRECT_URL' ] === '/app.style.css' ) {

          do_action( 'serve_custom_assets' );

          // WordPress will try to make it 404.
          http_response_code( 200 );

          // Set Some Headers.
          header( 'Cache-Control: public' );
          header( 'Content-Type: text/css' );
          header( 'Expires: 0' );
          header( 'Pragma: public' );

          // Output CSS.
          die( get_theme_mod( 'custom-style' ) );

        }

        // Serve JavaScript.
        if( isset( $_SERVER[ 'REDIRECT_URL' ] ) && $_SERVER[ 'REDIRECT_URL' ] === '/app.script.js' ) {

          // do_action( 'serve_custom_assets' );

          // WordPress will try to make it 404.
          http_response_code( 200 );

          // Set Some Headers.
          header( 'Cache-Control: public' );
          header( 'Content-Type: application/javascript' );
          header( 'Expires: 0' );
          header( 'Pragma: public' );

          // Output CSS.
          die( get_theme_mod( 'custom-script' ) );

        }

      }
    
    }
    
  }

}


      
<?php
/**
 * Custom Styles Customizer
 * Uses theme customization API
 *
 * @author usabilitydynamics@UD
 * @see https://codex.wordpress.org/Theme_Customization_API
 * @version 0.1
 * @module UsabilityDynamics\AMD
 */
namespace UsabilityDynamics\AMD {

  if( !class_exists( 'UsabilityDynamics\AMD\Style' ) ) {

    class Style {
    
      var $args = false;
    
      /**
       * Constructor
       *
       */
      function __construct( $args = array() ) {
        
        $this->args = shortcode_atts( array(
          'name'  => 'custom_style',
          'deps'  => array(),
          'version' => '1.0'
        ), $args );
        
        //** Enqueue Frontend Style. */
        add_action( 'wp_enqueue_scripts', function() {
          wp_enqueue_style( "wp-amd-{$this->args[ 'name' ]}", home_url() . "/wp-admin/admin-ajax.php?action=wp_amd_{$this->args[ 'name' ]}", $this->args[ 'deps' ], $this->args[ 'version' ] );
        });
        
        //** Renders Custom Styles */
        add_action( "wp_ajax_wp_amd_{$this->args[ 'name' ]}", array( &$this, "render_styles" ) );
        add_action( "wp_ajax_nopriv_wp_amd_{$this->args[ 'name' ]}", array( &$this, "render_styles" ) );
        
        //** Adds settings to customizer */
        if( !did_action( 'customize_register' ) ) {
          add_action( 'customize_register', array( &$this, 'customize_register' ) );
        }
        
      }
      
      /**
       * All our sections, settings, and controls are added here
       *
       * @param object $wp_customize Instance of the WP_Customize_Manager class
       * @see wp-includes/class-wp-customize-manager.php
       */
      function customize_register( $wp_customize ) {
        
        //** Configure Section. */
        $wp_customize->add_section( 'amd_custom_style', array(
          'title'    => __( 'Custom Styles' ),
          'description' => __( 'Handle custom CSS styles that will be loaded after all other styles.' ),
          'capability' => 'edit_theme_options',
          'priority' => -20
        ));
        
        //** Stores raw CSS. */
        $wp_customize->add_setting( 'amd_css_editor', array(
          'capability' => 'edit_theme_options',
          'transport' => 'postMessage'
        ));
        
        //** Input for CSS Code. */
        $wp_customize->add_control( new Customize_Editor_Control( $wp_customize, 'amd_css_editor', array(
          'label'   => __( 'CSS' ),
          'section' => 'amd_custom_style',
          'priority' => 10
        )));
        
        
        /*
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
        
        //*/
        
        return $wp_customize;
      }
      
      /**
       * Renders Custom CSS File
       *
       * @author peshkov@UD
       */
      static function render_styles() {

        // Set Some Headers.
        header( 'Cache-Control: public' );
        header( 'Content-Type: text/css' );
        header( 'Expires: 0' );
        header( 'Pragma: public' );

        // Output CSS.
        die( get_theme_mod( 'amd_css_editor' ) );

      }
    
    }
    
  }

}


      
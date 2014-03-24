<?php
/**
 * Contains methods for customizing the theme customization screen.
 * 
 * @link http://codex.wordpress.org/Theme_Customization_API
 * @since 0.1.0
 * author peshkov@UD
 */
namespace UsabilityDynamics\Festival {

  /**
   * Carrington Builder functionality
   *
   */
  class Customizer {
  
    /**
     *
     *
     */
    public function __construct(  ) {
    
      add_action( 'customize_register', array( $this, 'register' ), 100 );
      add_action( 'customize_preview_init', array( $this, 'admin_scripts' ), 100 );

    }

    /**
     * This hooks into 'customize_register' (available as of WP 3.4) and allows
     * you to add new sections and controls to the Theme Customize screen.
     *
     * Note: To enable instant preview, we have to actually write a bit of custom
     * javascript. See live_preview() for more.
     *
     * @see add_action('customize_register',$func)
     *
     * @param \WP_Customize_Manager $wp_customize
     *
     * @link http://ottopress.com/2012/how-to-leverage-the-theme-customizer-in-your-own-themes/
     * @since MyTheme 1.0
     */
    public static function register( $wp_customize ) {
    
      //** Remove extra sections and settings */
      $wp_customize->remove_section( 'title_tagline' );
      $wp_customize->remove_section( 'static_front_page' );
      $wp_customize->remove_section( 'nav' );
      $wp_customize->remove_section( 'background_image' );
      $wp_customize->remove_section( 'colors' );
      
      //echo "<pre>"; print_r( $wp_customize ); echo "</pre>";die();
      
      //*************** Colors ***************/

      $wp_customize->add_section( 'festival_colors', array(
        'title'    => __( 'Colors' ),
        'priority' => 40,
      ) );
      
      $wp_customize->add_setting( 'header_banner_bg_color', array(
        'default'    => '#fcfcf9',
        'type'       => 'option',
        'capability' => 'edit_theme_options',
        'transport'  => 'postMessage',
      ) );
      
      $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'header_banner_bg_color', array(
        'label'    => __( 'Header Banner Background' ),
        'section'  => 'festival_colors',
        'settings' => 'header_banner_bg_color',
      ) ) );
      
      $wp_customize->add_setting( 'content_bg_color', array(
        'default'    => '#fcfcf9',
        'type'       => 'option',
        'capability' => 'edit_theme_options',
        'transport'  => 'postMessage',
      ) );

      $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'content_bg_color', array(
        'label'    => __( 'Content Background' ),
        'section'  => 'festival_colors',
        'settings' => 'content_bg_color',
      ) ) );
      
      $wp_customize->add_setting( 'footer_bg_color', array(
        'default'    => '#fcfcf9',
        'type'       => 'option',
        'capability' => 'edit_theme_options',
        'transport'  => 'postMessage',
      ) );

      $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'footer_bg_color', array(
        'label'    => __( 'Footer Background' ),
        'section'  => 'festival_colors',
        'settings' => 'footer_bg_color',
      ) ) );
      
      //*************** Images ***************/
      
      $wp_customize->add_section( 'festival_images', array(
        'title'          => __( 'Images' ),
        'priority'       => 60,
      ) );
      
    }

    /**
     * This outputs the javascript needed to automate the live settings preview.
     * Also keep in mind that this function isn't necessary unless your settings
     * are using 'transport'=>'postMessage' instead of the default 'transport'
     * => 'refresh'
     *
     * Used by hook: 'customize_preview_init'
     *
     * @see add_action('customize_preview_init',$func)
     * @since MyTheme 1.0
     */
    public static function admin_scripts() {
      wp_enqueue_script(
        'festival-themecustomizer', // Give the script a unique ID
        get_template_directory_uri() . '/scripts/app.admin.customize.js', // Define the path to the JS file
        array( 'jquery', 'customize-preview' ), // Define dependencies
        '', // Define a version (optional)
        true // Specify whether to put in footer (leave this true)
      );
    }

  }

}

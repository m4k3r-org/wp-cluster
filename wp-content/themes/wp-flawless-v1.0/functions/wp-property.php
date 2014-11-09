<?php

if( !class_exists( 'WPP_F' ) ) {
  return;
}


/**
 * Name: WP-Property Extensions
 * Description: Extra functionality for WP-Property elements.
 * Author: Usability Dynamics, Inc.
 * Version: 1.0
 *
 */


add_action( 'flawless_theme_setup', array( 'flawless_wpp_extensions', 'flawless_theme_setup' ) );

class flawless_wpp_extensions {

  /**
   * Highend Loader
   *
   * @action flawless_theme_setup ( 10 )
   * @since Flawless 0.2.3
   */
  static function flawless_theme_setup() {

    add_action( 'flawless::init_lower', array( 'flawless_wpp_extensions', 'flawless_init' ) );

  }


  /**
   * Primary Loader
   *
   * @action init ( 10 )
   * @since Flawless 0.3.7
   *
   */
  function flawless_init() {

    /** Must force WPP to run the template redirect of theme, otherwise it'll ignore it and not load styles */
    add_action( 'wpp_template_redirect_post_scripts', array( 'flawless_wpp_extensions', 'wpp_template_redirect_post_scripts' ) );

    /* Add Landing Page support for a property type */
    add_action( 'wpp_admin_tools_property_type_options', array( 'flawless_wpp_extensions', 'wpp_admin_tools_property_type_options' ) );

    /* Render Property Type in breadcrumb trail */
    add_action( 'flawless::breadcrumb_trail', array( 'flawless_wpp_extensions', 'breadcrumb_trail' ) );

    /* Add Flawless-compatible classes to menu items that are parents of WPP objects */
    add_filter( 'nav_menu_css_class', array( 'flawless_wpp_extensions','nav_menu_css_class' ), 10, 3 );

    /* {check for necessity} */
    add_filter( 'flawless_exclude_sidebar', array( 'flawless_wpp_extensions', 'flawless_exclude_sidebar' ), 10, 2 );

    add_filter( 'wpp::class::wpp_search_button', function( $curr ) {
      return $curr . ' btn btn-success';
    });

    add_filter( 'wpp::label::search', function( $curr ) {
      return 'Search Properties';
    });

  }


  /**
   * WPP-specific template_redirect hook ran before loading template
   *
   * @action template_redirect ( 10 )
   * @since Flawless 0.2.3
   *
   */
  static function wpp_template_redirect_post_scripts() {

  }


  /**
   * Add menu classes to menu ancestors of the current property when a property type landing page is set ( Flawless Feature )
   *
   * @since Flawless 0.2.3
   *
   */
  static function nav_menu_css_class( $classes, $item, $args ) {
    global $wpdb, $post, $wp_properties, $property;

    if( !$property || !$wp_properties[ 'extra' ][ 'property_type_landing_pages' ][ $post->property_type ] ) {
      return $classes;
    }

    /** Check if the currently rendered item is a child of this link */
    if( $item->object_id == $wp_properties[ 'extra' ][ 'property_type_landing_pages' ][ $post->property_type ] ) {
      $classes[] = 'current-page-ancestor current-menu-ancestor current-menu-parent current-page-parent current_page_parent flawless_ad_hoc_menu_parent';
    }

    return $classes;

  }


   /**
    * Modify breadcrumb trail for WPP Objects
    *
    * @since Flawless 0.2.3
    */
  function breadcrumb_trail( $html ) {
    global $post, $wp_properties;

    if( $post->post_type != 'property' ) {
      return $html;
    }

    if( empty( $post->property_type ) || empty( $wp_properties[ 'extra' ][ 'property_type_landing_pages' ][ $post->property_type ] ) ) {
      return $html;
    }

    $landing_page_id = $wp_properties[ 'extra' ][ 'property_type_landing_pages' ][ $post->property_type ];

    $url = get_permalink( $landing_page_id );
    $title = get_the_title( $landing_page_id );


    $html[ 'content_type_home' ] = '<a href="' . $url . '">' . $title . '</a>';

    return $html;

  }


  /**
   * Add option to Developer Tools to select a Landing Page for a property type
   *
   * @since Flawless 0.2.3
   */
  function wpp_admin_tools_property_type_options( $property_type ) {
    global $wp_properties;

    echo '<label>' . __( 'Landing page:' );

    flawless_theme::wp_dropdown_objects( array(
      'name' => 'wpp_settings[ extra ][ property_type_landing_pages ][ ' . $property_type . ' ]',
      'show_option_none' => __( '&mdash; Select &mdash;' ),
      'option_none_value' => '0',
      'post_type' => get_post_types( array( 'hierarchical' => true ) ),
      'selected' => $wp_properties[ 'extra' ][ 'property_type_landing_pages' ][ $property_type ]
    ) );

    echo '</label>';

  }


  /**
   * Hook into set_current_view() and manually exclude property-type specific sidebars
   *
   * Loaded before WPP loads values into $property
   *
   * @since Flawless 0.2.3
   */
  function flawless_exclude_sidebar( $default, $sidebar_id ) {
    global $post, $property;

    if( $post->post_type != 'property' ) {
      return $default;
    }

    $property_type = get_post_meta( $post->ID, 'property_type', true );

    if( strpos( $sidebar_id, 'pp_sidebar_' ) ) {
      if( $sidebar_id != 'wpp_sidebar_' . $property_type ) {
        return true;
      }

    }

  }

}






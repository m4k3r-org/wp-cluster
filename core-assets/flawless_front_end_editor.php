<?php
/**
 * Name: Flawless Front End Editor
 * Version: 1.0
 * Description: Adds Layout editor to front-end.
 * Author: Usability Dynamics, Inc.
 * Theme Feature: frontend-editor
 *
 */


class flawless_front_end_editor {

  /**
    * Feature Initializer
    *
    * @since Flawless 0.5.0
    */
  function __construct() {

    /**
      * Add Frontend Editor as a Theme Feature. If already declared as Available Theme Feature, the exiting value takes presedence.
      *
      * @since Flawless 0.5.0
      */
    add_filter( 'flawless::available_theme_features', function( $features ) {
      return array_merge( array( 'frontend-editor' => true ), (array) $features );
    });


    /**
      * {missing description}
      *
      * @todo admin_bar_menu should be using flawless_theme::add_to_navbar() which should then add the Editor link to the appropriate Navbar. - potanin@UD 6/9/2012
      * @since Flawless 0.5.0
      */
    add_action( 'flawless::theme_setup::after', function() {

      if( !current_theme_supports( 'frontend-editor' ) ) {
        return;
      }

      if( current_user_can( 'manage_options' ) ) {
      
        add_action( 'admin_bar_menu', array( 'flawless_front_end_editor', 'admin_bar_menu' ), 200 );
        add_action( 'flawless_ajax_action', array( 'flawless_front_end_editor', 'flawless_ajax_action' ), 200, 2 );

        add_action( 'flawless::wp_enqueue_scripts', function() {
          wp_enqueue_script( 'jquery-ud-frontend_editor',  flawless_theme::load( 'jquery.ud.frontend_editor.js', 'js' ) , array(
            'jquery-ui-slider',
            'jquery-ui-draggable',
            'jquery-ui-sortable',
            'jquery-ui-resizable'
          ), Flawless_Version, true );
        });

        add_action( 'flawless::wp_print_styles', function() {
          wp_enqueue_style( 'flawless-live-editor', flawless_theme::load( 'flawless-live-editor.css', 'css' ), array(), Flawless_Version, 'screen' );
        });

      }

    }, 200 );

  }


  /**
    * Add "Theme Options" link to admin bar.
    *
    * @since Flawless 1.0
    */
  static function admin_bar_menu( $wp_admin_bar ) {

    if ( current_user_can( 'manage_options' ) && !is_admin() ) {

      $wp_admin_bar->add_menu( array(
        'id' => 'edit_layout',
        'title' => '<span class="flawless_edit_layout" style="display:none">' .  __( 'Edit Layout', 'flawless' ) . '</span>',
        'href' => '#'
      ) );

    }

   }


  /**
    * Displays saved flex styles in dynamic CSS file.
    *
    * Styles are applicable to screen only, at the present time.
    * If no flex styles are set.
    *
    * @todo Add option to create different styles for screen sizes.  Right now everything we do is for monitors over 980, only.
    * @since Flawless 1.0
    */
  function flawless_flex_styles() {
    global $flawless;

    $flawless[ 'flex_layout' ] = array_filter( ( array ) $flawless[ 'flex_layout' ] );

    foreach( ( array ) $flawless[ 'flex_layout' ][ 'grid' ] as $rule => $css ) {
      $return[] = $rule . ": {\n" . $css . "\n}\n\n";
    }

    foreach( ( array ) $flawless[ 'flex_layout' ][ 'containers' ] as $type => $css ) {
      $return[] = 'div.flawless_dynamic_area[container_type="' . $type . '"]' . " {\n\t\t" . $css . "\n\t}";
    }

    foreach( ( array ) $flawless[ 'flex_layout' ][ 'modules' ] as $element_hash => $css ) {
      $return[] = 'div.flawless_module[element_hash="' . $element_hash . '"]' . " {\n\t\t" . $css . "\n\t}";
    }

    header ( 'content-type: text/css; charset: UTF-8' );
    header ( 'cache-control: must-revalidate' );
    header ( 'expires: ' . gmdate ( 'D, d M Y H:i:s', time() + 60 * 60 ) . ' GMT' );

    $output = "@media ( min-width: 980px ) { \n\n\t" . implode( "\n\n\t", ( array ) $return ) . "\n\n} ";

    return apply_filters( 'flawless_css_output', $output );

  }


  /**
   * {missing description}
   *
   * @since Flawless 1.0
   */
  function write_static_file() {



  }


  /**
   * {missing description}
   *
   * @since Flawless 1.0
   */
  function flawless_ajax_action( $default, $flawless ) {

    switch( $_REQUEST[ 'the_action' ] ) {

      case 'save_front_end_layout':

        $flawless[ 'flex_layout' ] = $_REQUEST[ 'styles' ];

        if( !empty( $flawless[ 'flex_layout' ] ) ) {
          update_option( 'flawless_settings', $flawless );

          flawless_front_end_editor::write_static_file();

          return array( 'success' => true );

        } else {

          return array( 'success' => false );

        }

      break;

      case 'delete_flex_settings':

        $flawless[ 'flex_layout' ] = array();
        update_option( 'flawless_settings', $flawless );

        return array(
          'success' => true,
          'css_class' => 'success',
          'message' => __( 'Flex Layout configuration removed.', 'flawless' )
        );

      break;


    }


  }

}


$flawless_front_end_editor = new flawless_front_end_editor();
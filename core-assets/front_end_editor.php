<?php
/**
 * Name: Flawless Front End Editor
 * Version: 1.0
 * Description: Adds Layout editor to front-end.
 * Author: Usability Dynamics, Inc.
 * Theme Feature: frontend-editor
 *
 */

add_action( 'flawless::theme_setup::after', array( 'flawless_front_end_editor', 'flawless_theme_setup' ), 200 );
add_filter( 'flawless::available_theme_features', array( 'flawless_front_end_editor','available_theme_features' ) );

class flawless_front_end_editor {

  /**
   * {}
   *
   */
  static function available_theme_features( $features ) {
    $features[ 'frontend-editor' ] = true;
    return $features;
  }

  /**
    * {missing description}
    *
    * @since Flawless 2.1
    */
  static function flawless_theme_setup() {

    if( !current_theme_supports( 'frontend-editor' ) ) {
      return;
    }

    if( current_user_can( 'manage_options' ) ) {
      add_action( 'admin_bar_menu', array( 'flawless_front_end_editor', 'admin_bar_menu' ), 200 );
      add_action( 'flawless_ajax_action', array( 'flawless_front_end_editor', 'flawless_ajax_action' ), 200, 2 );
      add_action( 'flawless::extra_local_assets', array( 'flawless_front_end_editor', 'wp_enqueue_editor_scripts' ), 100 );
    }

    add_action( 'flawless::extra_local_assets', array( 'flawless_front_end_editor', 'extra_local_assets' ), 100 );
    add_filter( 'parse_request', array( 'flawless_front_end_editor', 'parse_request' ) );

  }

  /**
    * {missing description}
    *
    * @since Flawless 2.1
    */
  function parse_request( $query ) {
   global $wp, $wp_query;

   if( $query->request == 'flawless-flex-styles.css' || $_GET[ 'flawless_asset' ] == 'flawless-flex-styles.css' ) {
      add_action( 'wp', create_function( '', 'status_header( 200 );' ) );
      die( flawless_front_end_editor::flawless_flex_styles() );
    }

  }

  /**
    * {missing description}
    *
    * @since Flawless 2.1
    */
  function wp_enqueue_editor_scripts() {

    wp_enqueue_script( 'jquery-ui-draggable' );
    wp_enqueue_script( 'jquery-ui-sortable' );
    wp_enqueue_script( 'jquery-ui-resizable' );
    wp_enqueue_script( 'jquery-ud-toolbar_status',  get_bloginfo( 'template_url' ) . '/js/jquery.ud.toolbar_status.js', array( 'jquery' ), Flawless_Version, true );
    wp_enqueue_script( 'jquery-ud-frontend_editor',  get_bloginfo( 'template_url' ) . '/js/jquery.ud.frontend_editor.js', array( 'jquery' ), Flawless_Version, true );

    wp_enqueue_style( 'flawless-live-editor', get_bloginfo( 'template_url' ) . '/css/flawless-live-editor.css', array(), Flawless_Version, 'screen' );

  }

  /**
    * Enqueue the dynamic CSS file.
    *
    * Checks if permalinks are on.  The $_GET method will work with or without permalinks.
    *
    * @since Flawless 2.1
    */
  function extra_local_assets() {

    if( get_option( 'permalink_structure' ) != '' ) {
      wp_enqueue_style( 'flawless-flex-styles', get_bloginfo( 'url' ) . '/flawless-flex-styles.css', array(), Flawless_Version, 'all' );
    } else {
      wp_enqueue_style( 'flawless-flex-styles', get_bloginfo( 'url' ) . '/?flawless_asset=flawless-flex-styles.css', array(), Flawless_Version, 'all' );
    }

  }


  /**
    * Add "Theme Options" link to admin bar.
    *
    * @since Flawless 2.1
    */
  static function admin_bar_menu( $wp_admin_bar ) {

    if ( current_user_can( 'manage_options' ) && !is_admin() ) {

      $wp_admin_bar->add_menu( array(
        'id' => 'edit_layout',
        'title' => '<span class="flawless_edit_layout">' .  __( 'Edit Layout', 'flawless' ) . '</span>',
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
    * @since Flawless 2.1
    */
  function flawless_flex_styles() {
    global $flawless;

    $flawless[ 'flex_layout' ] = array_filter( ( array ) $flawless[ 'flex_layout' ] );

    foreach( ( array ) $flawless[ 'flex_layout' ][ 'containers' ] as $type => $css ) {
      $return[] = 'div.flawless_dynamic_area[container_type="' . $type . '"] { ' . $css . ' } ';
    }

    foreach( ( array ) $flawless[ 'flex_layout' ][ 'modules' ] as $element_hash => $css ) {
      $return[] = 'div.flawless_module[element_hash="' . $element_hash . '"] { ' . $css . ' } ';
    }

    header ( 'content-type: text/css; charset: UTF-8' );
    header ( 'cache-control: must-revalidate' );
    header ( 'expires: ' . gmdate ( 'D, d M Y H:i:s', time() + 60 * 60 ) . ' GMT' );

    $output = '@media ( min-width: 980px ) { ' . implode( "\n", ( array ) $return ) . '} ';

    return apply_filters( 'flawless_css_output', $output );

  }


  /**
    * {missing description}
    *
    * @since Flawless 2.1
    */
  function flawless_ajax_action( $default, $flawless ) {

    switch( $_REQUEST[ 'the_action' ] ) {

      case 'save_front_end_layout':

        $flawless[ 'flex_layout' ] = $_REQUEST[ 'styles' ];

        if( !empty( $flawless[ 'flex_layout' ] ) ) {
          update_option( 'flawless_settings', $flawless );
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

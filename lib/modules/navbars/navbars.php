<?php
/**
 * Flawless
 *
 * @author potanin@UD
 * @version 0.0.1
 * @namespace Flawless
 */
namespace Flawless {

  /**
   * Navbars
   *
   * -
   *
   * @author potanin@UD
   * @version 0.1.0
   * @class Navbars
   */
  class Navbars {

    // Class Version.
    public $version = '0.1.1';

    /**
     * Constructor for the Navbars class.
     *
     * @author potanin@UD
     * @version 0.0.1
     * @method __construct
     *
     * @constructor
     * @for Navbars
     *
     * @param array $options
     */
    public function __construct( $options = array() ) {


      //** Process Navbar ( has to be called early in case it needs to deregister the admin bar
      if ( !is_admin() ) {
        add_filter( 'flawless::init_upper', array( __SELF__, 'init_upper' ) );
      }



    }


    /**
     * Renders the Navbar form the template part.
     *
     * @since 0.3.5
     */
    static function render_navbars( $args = false ) {
      global $flawless;

      $args = wp_parse_args( $args, array(
        'echo' => true
      ) );

      //** Prepare for rendering as a string. */
      $flawless[ 'navbar' ][ 'html' ] = implode( '', (array) $flawless[ 'navbar' ][ 'html' ] );
      $flawless[ 'mobile_navbar' ][ 'html' ] = implode( '', (array) $flawless[ 'mobile_navbar' ][ 'html' ] );

      ob_start();
      get_template_part( 'header-navbar' );
      $html = ob_get_contents();
      ob_end_clean();

      if ( !$args[ 'echo' ] ) {
        return $html;
      }

      echo $html;

    }


    /**
     * Determines if we have a Navbar, and if so, the type.
     *
     * Loads information into global variables, setups body classes, loads scripts, etc.
     * current_theme_supports() is ran within the function to give child themes the opportunity to remove support.
     *
     * @todo Navbar depth level of 1 is temporary - a callback must be added to render WP menus in TB format. - potanin@UD
     *
     * @method init_upper
     * @filter init ( 500 )
     * @author potanin@UD
     * @since 0.3.5
     */
    static function init_upper() {
      global $flawless;

      // Register Navigation Menus
      // @todo Migrate presentation-specific logic outside of this class; otherwise should make it easy to disable/enable in child theme.
      register_nav_menus(
        array(
          //'header-actions-menu' => __( 'Header Actions Menu', 'flawless' ),
          'header-menu' => __( 'Header Menu', 'flawless' ),
          //'header-sub-menu' => __( 'Header Sub-Menu', 'flawless' ),
          'footer-menu' => __( 'Footer Menu', 'flawless' ),
          //'bottom_of_page_menu' => __( 'Bottom of Page Menu', 'flawless' )
        )
      );


      if ( apply_filters( 'flawless::use_navbar', $flawless[ 'disabled_theme_features' ][ 'header-navbar' ] ) ) {
        return;
      }

      if ( current_theme_supports( 'header-navbar' ) ) {

        //** Disable WordPress Toolbar unless it is selected */
        if ( $flawless[ 'navbar' ][ 'type' ] != 'wordpress' ) {
          remove_action( 'init', '_wp_admin_bar_init' );
        }

      }

      /**
       * Bind to Template Redirect for rest of Navbar Loading since we need the $post object.
       *
       * @author potanin@UD
       * @since 0.0.6
       */
      add_action( 'template_redirect', function () {
        global $flawless;

        if ( current_theme_supports( 'header-navbar' ) && $flawless[ 'navbar' ][ 'type' ] != 'wordpress' ) {

          $flawless[ 'navbar' ][ 'html' ] = array();

          if ( wp_get_nav_menu_object( $flawless[ 'navbar' ][ 'type' ] ) ) {
            $flawless[ 'navbar' ][ 'html' ][ 'left' ] = wp_nav_menu( array(
              'menu' => $flawless[ 'navbar' ][ 'type' ],
              'menu_class' => 'nav',
              'fallback_cb' => false,
              'echo' => false,
              'container' => false,
              'depth' => 1
            ) );
          }

          $flawless[ 'navbar' ][ 'html' ] = apply_filters( 'flawless::navbar_html', $flawless[ 'navbar' ][ 'html' ] );

          if ( is_array( $flawless[ 'navbar' ][ 'html' ] ) ) {

            /**Place edit layout in the navbar*/
            if ( $flawless[ 'navbar' ][ 'show_editlayout' ] == 'true' ) {
              if ( current_user_can( 'manage_options' ) && !is_admin() ) {

                array_push( $flawless[ 'navbar' ][ 'html' ], '<li><a class="" href="' . get_edit_post_link() . '">' . __( 'Edit Page', 'flawless' ) . '</a></li>' );

                array_push( $flawless[ 'navbar' ][ 'html' ], '<li><a class="flawless_edit_layout hidden" href="#flawless_action">' . __( 'Edit Layout', 'flawless' ) . '</a></li>' );

              }
            }

            foreach ( $flawless[ 'navbar' ][ 'html' ] as $key => &$value ) {

              if ( empty( $value ) ) {
                unset( $flawless[ 'navbar' ][ 'html' ][ $key ] );
              }

              if ( is_array( $value ) ) {
                $value = implode( '', $value );
              }

              $class = $key == "right" ? "pull-right" : "";
              $value = "<div class=\"nav-collapse {$class}\"><ul class=\"nav\">{$value}</ul></div>";

            }
          }

          //** Clean up Navbar */
          $flawless[ 'navbar' ][ 'html' ] = array_filter( (array) $flawless[ 'navbar' ][ 'html' ] );

        }

        if ( current_theme_supports( 'mobile-navbar' ) ) {

          /* Mobile Navbar is limited to one depth level */
          if ( wp_get_nav_menu_object( $flawless[ 'mobile_navbar' ][ 'type' ] ) ) {
            $flawless[ 'mobile_navbar' ][ 'html' ][ 'left' ] = wp_nav_menu( array(
              'menu' => $flawless[ 'mobile_navbar' ][ 'type' ],
              'menu_class' => 'nav',
              'fallback_cb' => false,
              'echo' => false,
              'depth' => 1
            ) );
          }
          $flawless[ 'mobile_navbar' ][ 'html' ] = apply_filters( 'flawless::mobile_navbar_html', $flawless[ 'mobile_navbar' ][ 'html' ] );

          if ( is_array( $flawless[ 'mobile_navbar' ][ 'html' ] ) ) {
            foreach ( $flawless[ 'mobile_navbar' ][ 'html' ] as $key => &$value ) {

              if ( empty( $value ) ) {
                unset( $flawless[ 'mobile_navbar' ][ 'html' ][ $key ] );
              }

              if ( is_array( $value ) ) {
                $value = implode( '', $value );
              }

              $class = $key == 'right' ? 'pull-right' : '';
              $value = "<div class=\"nav-collapse nav-collapse-mobile {$class}\"><ul class=\"nav\">{$value}</ul></div>";

            }
          }

          $flawless[ 'mobile_navbar' ][ 'html' ] = array_filter( (array) $flawless[ 'mobile_navbar' ][ 'html' ] );
        }

        if ( !empty( $flawless[ 'navbar' ][ 'html' ] ) ) {
          $flawless[ 'current_view' ][ 'body_classes' ][ ] = 'have-navbar';
        } else {
          unset( $flawless[ 'navbar' ][ 'html' ] );
        }

        if ( !empty( $flawless[ 'mobile_navbar' ][ 'html' ] ) ) {
          $flawless[ 'current_view' ][ 'body_classes' ][ ] = 'have-mobile-navbar';
        } else {
          unset( $flawless[ 'mobile_navbar' ][ 'html' ] );
        }

        if ( $flawless[ 'navbar' ][ 'html' ] || $flawless[ 'mobile_navbar' ][ 'html' ] ) {
          add_action( 'header-navbar', array( __CLASS__, 'render_navbars' ) );
        }

      } );

    }

    /**
     * Not Used. Adds an item to the Navbar.
     *
     * Needs to be called before init ( 500 )
     *
     * @todo Finish function and update the way Navbar items are added. - potanin@UD 4/17/12
     * @since 0.5.0
     */
    static function add_to_navbar( $html, $args = false ) {
      global $flawless;

      $args = wp_parse_args( $args, array(
        'order' => 100,
        'position' => 'left',
        'navbar' => array( 'navbar' )
      ) );

      foreach ( (array) $args[ 'navbar' ] as $navbar_type ) {
        $flawless[ $navbar_type ][ 'html' ] .= 'start' . $html . '|';
      }

    }


  }

}
<?php
/**
 * Presentation
 *
 * @namespace Flawless
 * @class Flawless\Shortcode
 *
 * @user: potanin@UD
 * @date: 8/31/13
 * @time: 10:33 AM
 */
namespace Flawless {

  /**
   * Shortcode Management
   *
   * -
   *
   * @module Flawless
   * @class Theme
   */
  class Theme {

    // Class Version.
    public $version = '0.1.1';

    /**
     * Constructor
     *
     */
    public function __construct( $options = false ) {
      add_filter( 'flawless::template_redirect', array( $this, 'template_redirect' ) );
      add_filter( 'flawless::theme_setup', array( $this, 'theme_setup' ), 100 );
      add_filter( 'flawless::init_upper', array( $this, 'init_upper' ) );

      add_filter( 'wp_title', array( __SELF__, 'wp_title' ) );
      add_action( 'wp_head', array( __SELF__, 'wp_head' ) );

      add_filter( 'post_link', array( __SELF__, 'filter_post_link' ), 10, 2 );
      add_filter( 'post_type_link', array( __SELF__, 'filter_post_link' ), 10, 2 );
      add_filter( 'post_type_link', array( __SELF__, 'filter_post_link' ), 10, 2 );

      add_filter( 'widget_text', 'do_shortcode' );


    }

    /**
     * Setup theme features using the WordPress API as much as possible.
     *
     * Have to be run on after_setup_theme() level.
     *
     * @todo Should have some support for bootstrap content styles. - potanin@UD 6/10/12
     * @updated 0.0.6
     * @since 0.0.2
     */
    public function theme_setup( &$flawless ) {

      //** Load styles to be used by editor */
      add_editor_style( array(
        'ux/styles/content.css',
        'ux/styles/editor-style.css'
      ));

      if ( $flawless[ 'color_scheme' ] ) {
        $flawless[ 'color_scheme_data' ] = Theme::get_color_schemes( $flawless[ 'color_scheme' ] );
      }

      $flawless[ 'current_theme_options' ] = array_merge( (array) $flawless[ 'theme_data' ], (array) $flawless[ 'child_theme_data' ], (array) $flawless[ 'color_scheme_data' ] );

      if ( !empty( $flawless[ 'current_theme_options' ][ 'Google Fonts' ] ) ) {
        $flawless[ 'current_theme_options' ][ 'Google Fonts' ] = Utility::trim_array( explode( ', ', trim( $flawless[ 'current_theme_options' ][ 'Google Fonts' ] ) ) );
      }

      if ( !empty( $flawless[ 'current_theme_options' ][ 'Supported Features' ] ) ) {
        $flawless[ 'current_theme_options' ][ 'Supported Features' ] = Utility::trim_array( explode( ', ', trim( $flawless[ 'current_theme_options' ][ 'Supported Features' ] ) ) );
      }

      define( 'HEADER_TEXTCOLOR', '000' );
      define( 'HEADER_IMAGE', apply_filters( 'flawless::header_image', '' ) );
      define( 'HEADER_IMAGE_WIDTH', apply_filters( 'flawless::header_image_width', $flawless[ 'header_image_width' ] ? $flawless[ 'header_image_width' ] : 1090 ) );
      define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'flawless::header_image_height', $flawless[ 'header_image_height' ] ? $flawless[ 'header_image_height' ] : 314 ) );

      add_image_size( 'large-feature', HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

      //** All Available Theme Features */
      $flawless[ 'available_theme_features' ][ 'custom-skins' ] = true;
      $flawless[ 'available_theme_features' ][ 'post-thumbnails' ] = true;
      $flawless[ 'available_theme_features' ][ 'custom-background' ] = true;
      $flawless[ 'available_theme_features' ][ 'custom-header' ] = true;
      $flawless[ 'available_theme_features' ][ 'automatic-feed-links' ] = true;
      $flawless[ 'available_theme_features' ][ 'header-dropdowns' ] = true;
      $flawless[ 'available_theme_features' ][ 'header-logo' ] = true;
      $flawless[ 'available_theme_features' ][ 'header-navbar' ] = true;
      $flawless[ 'available_theme_features' ][ 'header-search' ] = true;
      $flawless[ 'available_theme_features' ][ 'header-text' ] = true;
      $flawless[ 'available_theme_features' ][ 'mobile-navbar' ] = true;
      $flawless[ 'available_theme_features' ][ 'footer-copyright' ] = true;
      $flawless[ 'available_theme_features' ][ 'extended-taxonomies' ] = true;
      $flawless[ 'available_theme_features' ] = apply_filters( 'flawless::available_theme_features', $flawless[ 'available_theme_features' ] );

      //** Load all Available Theme featurse */
      foreach ( (array) $flawless[ 'available_theme_features' ] as $feature => $always_true ) {
        add_theme_support( $feature );
      }

      //** Remove any explicitly disabled Features */
      foreach ( (array) $flawless[ 'disabled_theme_features' ] as $feature => $not_false ) {

        if ( $not_false !== 'false' ) {
          return;
        }

        remove_theme_support( $feature );

        if ( in_array( $feature, array( 'custom-background', 'custom-header', 'editor-style', 'widgets', 'menus' ) ) ) {

          switch ( $feature ) {

            case 'custom-background':
              remove_custom_background();
              break;

            case 'custom-header':
              remove_custom_image_header();
              break;

          }

        }

      }

      do_action( 'flawless::setup_theme_features::after', $flawless );

      return $flawless;

    }

    /**
     * Initializer.
     *
     * @method init_upper
     * @for Theme
     */
    public function init_upper( &$flawless ) {

      add_filter( 'request', array( __SELF__, 'request' ), 0 );

    }

    /**
     * Frontend Initializer
     *
     * @method template_redirect
     * @for Theme
     */
    public function template_redirect( &$flawless ) {
      global $wp_query;

      add_filter( 'wp_nav_menu_args', array( __CLASS__, 'wp_nav_menu_args' ), 5 );
      add_filter( 'walker_nav_menu_start_el', array( __CLASS__, 'walker_nav_menu_start_el' ), 5, 4 );
      add_filter( 'nav_menu_css_class', array( __CLASS__, 'nav_menu_css_class' ), 5, 3 );

      add_filter( 'body_class', array( __SELF__, 'body_class' ) );
      add_filter( 'post_class', array( __CLASS__, 'post_class' ), 10, 3 );

      add_filter( 'wp_title', array( __CLASS__, 'wp_title' ), 10, 3 );

      add_action( 'flawless::content_container_top', array( __CLASSS__, 'content_container_top' ) );



      $wp_query->query_vars[ 'flawless' ] = &$flawless;

    }

    /**
     * content_container_top
     *
     * ?
     *
     * @method content_container_top
     * @for Theme
     */
    static function content_container_top() {
      flawless_primary_notice_container( '' );
    }

    /**
     * Tweaks the default title. In most cases a specialty plugin will be used.
     *
     * @since 0.3.7
     */
    static function wp_title( $current_title, $sep, $seplocation ) {

      $title = array();

      if ( is_home() || is_front_page() ) {
        $title[ ] = get_bloginfo( 'name' );

        if ( get_bloginfo( 'description' ) ) {
          $title[ ] = get_bloginfo( 'description' );
        }

      } else {
        $title[ ] = $current_title;
        $title[ ] = get_bloginfo( 'name' );
      }

      return trim( implode( ' - ', $title ) );

    }

    /**
     * Front-end Header Things
     *
     * @since 0.0.2
     */
    public function wp_head() {
      Log::add( 'Executed: Flawless::wp_head();' );

      $html = array( '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>' );

      //** Check for and load favico.ico */
      if ( file_exists( untrailingslashit( get_stylesheet_directory() ) . '/favicon.ico' ) ) {
        $html[] = '<link rel="shortcut icon" href="' . get_bloginfo( 'stylesheet_directory' ) . '/favicon.ico" type="image/x-icon" />';
      };

      if ( file_exists( untrailingslashit( get_stylesheet_directory() ) . '/apple-touch-icon.png' ) ) {
        $html[] = '<link type="image/png" rel="apple-touch-icon" href="' . untrailingslashit( get_stylesheet_directory_uri() ) . '"/>';
      }

      echo implode( "\n", $html );

    }

    /**
     * Determines if search request exists but it's empty, we do 'hack' to show Search result page.
     *
     * @author peshkov@UD
     */
    public function request( $query_vars ) {

      if ( isset( $_GET[ 's' ] ) && empty( $_GET[ 's' ] ) ) {
        $query_vars[ 's' ] = " ";
      }

      return $query_vars;

    }

    /**
     * Filters a post permalink to replace the tag placeholder with the first
     * used term from the taxonomy in question.
     *
     * @source http://www.viper007bond.com/2011/10/07/code-snippet-helper-class-to-add-custom-taxonomy-to-post-permalinks/
     * @since 0.5.0
     */
    static function filter_post_link( $permalink, $post ) {
      global $flawless;

      foreach ( (array) $flawless[ 'taxonomies' ] as $taxonomy => $data ) {

        if ( false === strpos( $permalink, $data[ 'rewrite_tag' ] ) ) {
          continue;
        }

        $terms = get_the_terms( $post->ID, $taxonomy );
        if ( empty( $terms ) ) {
          $permalink = str_replace( $data[ 'rewrite_tag' ], $taxonomy, $permalink );
        } else {
          $first_term = array_shift( $terms );
          $permalink = str_replace( $data[ 'rewrite_tag' ], $first_term->slug, $permalink );
        }

      }

      return $permalink;

    }

    /**
     * Adds content-specific classes
     *
     */
    static function post_class( $classes ) {

      if ( has_post_thumbnail() ) {
        $classes[ ] = 'has-img';
      } else {
        $classes[ ] = 'has-not-img';
      }

      return $classes;

    }

    /**
     * Scans asset directories for available color schemes, or can be used to get information about a specific skin.
     *
     * Scans child theme first.
     *
     * @since 0.0.2
     */
    static function get_color_schemes( $requested_scheme = false ) {
      global $flawless;

      $files = wp_cache_get( 'color_schemes', 'flawless' );

      if ( !$files ) {

        //** Reverse so child theme gets scanned first */
        $skin_directories = apply_filters( 'flawless::skin_directories', array_reverse( $flawless[ 'asset_directories' ] ) );

        foreach ( (array) $skin_directories as $path => $url ) {

          if ( !is_dir( $path ) || !$resource = opendir( $path ) ) {
            continue;
          }

          while ( false !== ( $file = readdir( $resource ) ) ) {

            if ( $file == "." || $file == ".." || strpos( $file, 'skin-' ) !== 0 || substr( strrchr( $file, '.' ), 1 ) != 'css' ) {
              continue;
            }

            $file_data = array_filter( (array) @get_file_data( $path . '/' . $file, $flawless[ 'default_header' ][ 'themes' ], 'themes' ) );

            if ( empty( $file_data ) ) {
              continue;
            }

            $file_data[ 'css_path' ] = $path . '/' . $file;
            $file_data[ 'css_url' ] = $url . '/' . $file;

            $potential_thumbnails = array(
              str_replace( '.css', '.jpg', $file ),
              str_replace( '.css', '.png', $file )
            );

            if ( !empty( $file_data[ 'Thumbnail' ] ) ) {
              $potential_thumbnails[ ] = $file_data[ 'Thumbnail' ];
              array_reverse( $potential_thumbnails );
            }

            foreach ( (array) $potential_thumbnails as $thumbnail_filename ) {
              foreach ( (array) $skin_directories as $thumb_path => $thumb_url ) {
                if ( file_exists( trailingslashit( $thumb_path ) . '/' . $thumbnail_filename ) ) {
                  $file_data[ 'thumb_url' ] = $thumb_url . '/' . $thumbnail_filename;
                  break;
                }
              }
            }

            if ( !isset( $files[ $file ] ) ) {
              $files[ $file ] = array_filter( ( array) $file_data );
            }

          }
        }

        $files = array_filter( (array) $files );

      }

      wp_cache_set( 'color_schemes', $files, 'flawless' );

      if ( $requested_scheme && $files[ $requested_scheme ] ) {
        return $files[ $requested_scheme ];
      }

      if ( empty( $files ) ) {
        return false;
      }

      return $files;

    }

    /**
     * Add all the body classes
     *
     * @since 0.2.5
     * @author potanin@UD
     */
    static function body_class( $classes, $class ) {
      global $flawless;

      //** Added classes to body */
      foreach ( (array) $flawless[ 'current_view' ][ 'body_classes' ] as $class ) {
        $classes[ ] = $class;
      }

      if ( $flawless[ 'visual_debug' ] == 'true' ) {
        $classes[ ] = 'flawless_visual_debug';
      }

      $classes = apply_filters( 'flawless::body_class', $classes, $class );

      return array_unique( $classes );

    }

    /**
     * {need description}
     *
     * Adds a special class to menus that display descriptions for the individual menu items
     *
     * @since 0.0.2
     *
     */
    static function wp_nav_menu_args( $args ) {
      global $flawless;

      if ( $flawless[ 'menus' ][ $args[ 'theme_location' ] ][ 'show_descriptions' ] == 'true' ) {
        $args[ 'menu_class' ] = $args[ 'menu_class' ] . ' menu_items_have_descriptions';
      }

      return $args;

    }

    /**
     * {need description}
     *
     * @since 0.0.2
     *
     */
    static function walker_nav_menu_start_el( $item_output, $item, $depth, $args ) {
      global $flawless;

      //** Do not add description if this is not a top level menu item */
      if ( $item->menu_item_parent || $flawless[ 'menus' ][ $args->theme_location ][ 'show_descriptions' ] != 'true' ) {
        return $item_output;
      }

      $char_limit = 50;

      $description = substr( $item->description, 0, $char_limit ) . ( strlen( $item->description ) > $char_limit ? '...' : '' );

      $trigger = '</a>' . $args->after;

      //** Inject description HTML by identifying the $args->after */
      $item_output = str_replace( $trigger, $trigger . ( $description ? '<span class="menu_item_description">' . $description . '</span>' : '' ), $item_output );

      return $item_output;

    }

    /**
     * Modified front-end menus and adds extra classes
     *
     * @todo Find way to inexpensively figure out if current item is last and add a class.
     * @since 0.0.2
     */
    static function nav_menu_css_class( $classes, $item, $args ) {
      global $post, $flawless, $wpdb;

      $total_items = $wpdb->get_var( "SELECT count FROM {$wpdb->term_relationships} tr LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE object_id = {$item->db_id} AND taxonomy = 'nav_menu' " );

      if ( !$item->menu_item_parent ) {
        $classes[ ] = 'top_level_item';
      } else {
        $classes[ ] = 'sub_menu_level_item';
      }

      if ( $item->menu_order == 1 ) {
        $classes[ ] = 'first';

      }
      if ( $item->menu_order == $total_items ) {
        $classes[ ] = 'last';
      }

      //** Check if the currently rendered item is a child of this link */
      if ( untrailingslashit( $item->url ) == untrailingslashit( $flawless[ 'post_types' ][ $post->post_type ][ 'archive_url' ] ) ) {

        $classes[ ] = 'current-page-ancestor current-menu-ancestor current-menu-parent current-page-parent current_page_parent flawless_ad_hoc_menu_parent';

        //** This menu item is an ad-hoc parent of something, we need to update parent elements as well */
        if ( $item->menu_item_parent ) {

        }

      }

      return $classes;

    }

  }

}